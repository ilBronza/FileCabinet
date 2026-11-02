<?php

namespace IlBronza\FileCabinet\Tests\Feature;

use IlBronza\FileCabinet\FileCabinetServiceProvider;
use IlBronza\FileCabinet\Helpers\DossierPopulatorHelper;
use IlBronza\FileCabinet\Helpers\DossierrowFormFieldHelper;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\FileCabinet\Offline\OfflineAccess;
use IlBronza\FileCabinet\Offline\OfflineQuestionnaire;
use IlBronza\FileCabinet\Providers\RowTypes\RowTypeRegistry;
use IlBronza\FileCabinet\Providers\RowTypes\Rows\FormrowText;
use IlBronza\FileCabinet\Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UnavailableRowTypeTest extends TestCase
{
	protected function setUp() : void
	{
		parent::setUp();
		(new FileCabinetServiceProvider($this->app))->register();
		app('translator')->addNamespace('filecabinet', __DIR__ . '/../../resources/lang');
		app()->setLocale('it');
		DB::table(config('filecabinet.models.formrow.table'))->insert([
			'id' => 'missing-formrow', 'form_id' => 'form', 'name' => 'Old field', 'slug' => 'old',
			'type' => 'room', 'required' => false, 'parameters' => '{"untouched":["original"]}',
		]);
		DB::table(config('filecabinet.models.dossierrow.table'))->insert([
			'id' => 'missing-row', 'formrow_id' => 'missing-formrow', 'string' => 'stored-uuid',
			'text' => '["historical-uuid"]', 'boolean' => false, 'decimal' => 12.5,
		]);
	}

	protected function row() : Dossierrow { return Dossierrow::with('formrow')->findOrFail('missing-row'); }

	public function test_missing_type_is_read_only_unverifiable_and_explains_its_package() : void
	{
		$row = $this->row();
		$this->assertStringContainsString('ilbronza/warehouse', $row->renderFormfieldValue());
		$this->assertTrue($row->isFormfieldDisabled());
		$this->assertTrue($row->isFormfieldReadOnly());
		$this->assertFalse($row->isCompleted());
		$this->assertFalse($row->isPopulated());
		$this->assertTrue($row->isMissing());
		$this->assertNotEmpty($row->getFormfieldProblems());
		$this->assertSame(['prohibited'], $row->getFormfieldRules());
		$field = DossierrowFormFieldHelper::createFieldFromDossierrow($row);
		$this->assertTrue($field->isDisabled());
		$this->assertTrue($field->isReadOnly());
		$this->assertFalse($field->isRepeatable());
		$this->assertFalse($field->hasUpdateEditor());

		$snapshot = (new OfflineQuestionnaire(new OfflineAccess()))->field($row);
		$this->assertFalse($snapshot['editable']);
		$this->assertStringContainsString('ilbronza/warehouse', $snapshot['reason']);
	}

	public function test_all_store_clear_and_set_paths_preserve_every_value_and_parameter() : void
	{
		$row = $this->row();
		$before = $row->getRawOriginal();
		$params = $row->getFormrow()->getRawOriginal('parameters');
		$this->assertFalse($row->storeRowValue('replacement'));
		$this->assertFalse($row->emptyRowValue());
		$row->setValue('replacement');
		$this->assertSame($before, $row->getAttributes());
		$this->assertSame($before, $row->fresh()->getAttributes());
		$this->assertSame($params, $row->getFormrow()->fresh()->getRawOriginal('parameters'));
		$this->assertTrue(validator([], ['value' => $row->getFormfieldRules()])->passes());
		$this->assertTrue(validator(['value' => 'changed'], ['value' => $row->getFormfieldRules()])->fails());
	}

	public function test_reinstalling_the_type_restores_the_same_saved_row_without_conversion() : void
	{
		$row = $this->row();
		$this->assertFalse($row->getRowType()->isAvailable());
		app(RowTypeRegistry::class)->register('room', FormrowText::class, 'ilbronza/warehouse');
		$this->assertTrue($row->getRowType()->isAvailable());
		$this->assertSame('stored-uuid', $row->getValue());
		$this->assertSame(['untouched' => ['original']], $row->getFormrow()->getSpecialParameters());
	}

	public function test_missing_type_cannot_be_changed_or_have_its_parameters_rewritten() : void
	{
		$formrow = $this->row()->getFormrow();
		$formrow->type = 'text';
		$this->expectException(ValidationException::class);
		$formrow->save();
	}

	public function test_missing_catalog_types_explain_their_optional_packages() : void
	{
		$registry = app(RowTypeRegistry::class);
		foreach (['contracttype' => 'ilbronza/operators', 'roomType' => 'ilbronza/warehouse'] as $type => $package)
		{
			$this->assertFalse($registry->make($type)->isAvailable());
			$this->assertSame($package, $registry->info($type)['package']);
			$this->assertStringContainsString($package, $registry->make($type)->message);
		}
	}

	public function test_unknown_third_party_type_also_has_a_safe_fallback() : void
	{
		$type = app(RowTypeRegistry::class)->make('thirdPartyRemoved');
		$this->assertFalse($type->isAvailable());
		$this->assertStringContainsString('thirdPartyRemoved', $type->message);
	}

	public function test_missing_type_parameters_cannot_be_rewritten() : void
	{
		$formrow = $this->row()->getFormrow();
		$formrow->parameters = ['replacement' => true];
		$this->expectException(ValidationException::class);
		$formrow->save();
	}

	public function test_renaming_a_missing_type_keeps_the_original_type_and_parameters() : void
	{
		$formrow = $this->row()->getFormrow();
		$before = $formrow->getRawOriginal('parameters');
		$formrow->name = 'Renamed';
		$formrow->save();
		$this->assertSame('room', $formrow->fresh()->type);
		$this->assertSame($before, $formrow->fresh()->getRawOriginal('parameters'));
	}

	public function test_other_fields_can_be_saved_while_the_missing_field_stays_untouched() : void
	{
		$missing = $this->row();
		$normalForm = (new Formrow())->forceFill(['id' => 'text-form', 'type' => 'text', 'required' => false]);
		DB::table(config('filecabinet.models.dossierrow.table'))->insert(['id' => 'text-row', 'formrow_id' => 'text-form']);
		$normal = Dossierrow::findOrFail('text-row')->setRelation('formrow', $normalForm);
		$dossier = (new UnavailableTestDossier())->setRelation('dossierrows', collect([$missing, $normal]));
		$helper = (new UnavailableTestPopulator())->setDossier($dossier)->setBindingParameters(['text-row' => 'Updated']);
		$before = $missing->getAttributes();
		$helper->populate();
		$this->assertSame('Updated', $normal->fresh()->string);
		$this->assertSame($before, $missing->fresh()->getAttributes());
		$this->assertFalse($dossier->checkForCompletion(false));
	}
}

class UnavailableTestPopulator extends DossierPopulatorHelper
{
	public function setDossierPopulated() {}
}

class UnavailableTestDossier extends Dossier
{
	public function setNotPopulated(bool $save = true) { $this->populated_at = null; }
}
