<?php

namespace IlBronza\FileCabinet\Tests\Feature;

use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\FileCabinet\Tests\TestCase;
use Illuminate\Support\Facades\DB;

class FormrowBooleanTest extends TestCase
{
	protected function defineEnvironment($app) : void
	{
		parent::defineEnvironment($app);
		$config = require __DIR__ . '/../../config/filecabinet.php';
		$app['config']->set('filecabinet.formrows', $config['formrows']);
	}

	private function formrow(array $parameters = []) : Formrow
	{
		$formrow = new Formrow();
		$formrow->forceFill([
			'type' => 'boolean',
			'required' => false,
			'parameters' => $parameters,
		]);

		return $formrow;
	}

	private function render(Formrow $formrow, mixed $value) : ?string
	{
		$dossierrow = new Dossierrow();
		$dossierrow->setRelation('formrow', $formrow);
		$dossierrow->boolean = $value;

		return $dossierrow->renderFormfieldValue();
	}

	private function storedFormrow(array $parameters = []) : Formrow
	{
		DB::table(config('filecabinet.models.form.table'))->insert([
			'id' => 'form', 'name' => 'Form',
		]);
		DB::table(config('filecabinet.models.formrow.table'))->insert([
			'id' => 'boolean-row', 'form_id' => 'form', 'name' => 'Boolean row',
			'slug' => 'boolean-row', 'type' => 'boolean',
			'parameters' => json_encode($parameters),
		]);

		return Formrow::findOrFail('boolean-row');
	}

	public function test_package_defaults_render_booleans_and_database_values_and_leave_null_empty() : void
	{
		$formrow = $this->formrow();

		foreach ([true, 1, '1'] as $value)
			$this->assertSame('Sì', $this->render($formrow, $value));

		foreach ([false, 0, '0'] as $value)
			$this->assertSame('No', $this->render($formrow, $value));

		$this->assertNull($this->render($formrow, null));
	}

	public function test_global_config_changes_apply_without_a_formrow_override() : void
	{
		$formrow = $this->formrow();
		config(['filecabinet.formrows.boolean' => [
			'render_true' => '1', 'render_false' => '0',
		]]);

		$this->assertSame('1', $this->render($formrow, true));
		$this->assertSame('0', $this->render($formrow, false));
		$this->assertNull($this->render($formrow, null));
	}

	public function test_formrow_overrides_are_independent_and_do_not_affect_other_rows() : void
	{
		$formrow = $this->formrow(['render_true' => 'Conforme']);
		$this->assertSame('Conforme', $this->render($formrow, true));
		$this->assertSame('No', $this->render($formrow, false));

		$formrow = $this->formrow(['render_true' => null, 'render_false' => '0']);
		$this->assertSame('Sì', $this->render($formrow, true));
		$this->assertSame('0', $this->render($formrow, false));
		$this->assertNull($this->render($formrow, null));

		$this->assertSame('Sì', $this->render($this->formrow(), true));
		$this->assertSame('No', $this->render($this->formrow(), false));
	}

	public function test_extra_fields_show_overrides_and_config_placeholders_without_changing_boolean_rules() : void
	{
		$formrow = $this->formrow(['render_true' => 'Conforme']);
		$this->assertTrue($formrow->hasSpecialParameters());

		$fields = $formrow->getRowType()->getSpecialParametersFields();
		$this->assertSame('Conforme', $fields['render_true']['value']);
		$this->assertSame('Sì', $fields['render_true']['placeholder']);
		$this->assertNull($fields['render_false']['value']);
		$this->assertSame('No', $fields['render_false']['placeholder']);

		foreach ($fields as $field)
		{
			$this->assertSame('text', $field['type']);
			$this->assertTrue(validator(['value' => '0'], ['value' => $field['rules']])->passes());
			$this->assertTrue(validator(['value' => null], ['value' => $field['rules']])->passes());
			$this->assertFalse(validator(['value' => ['invalid']], ['value' => $field['rules']])->passes());
		}

		$this->assertSame(['boolean', 'nullable'], $formrow->getFormfieldRules());
		$formrow->required = true;
		$this->assertSame(['boolean', 'required'], $formrow->getFormfieldRules());
	}

	public function test_extra_fields_persist_as_parameters_and_can_be_cleared_to_restore_config() : void
	{
		$formrow = $this->storedFormrow(['existing' => 'preserved']);
		$formrow->forceFill(['render_true' => 'Conforme', 'render_false' => 'Non conforme']);
		$formrow->save();

		$formrow = $formrow->fresh();
		$this->assertSame('preserved', $formrow->getSpecialParameter('existing'));
		$this->assertSame('Conforme', $this->render($formrow, true));
		$this->assertSame('Non conforme', $this->render($formrow, false));
		$this->assertArrayNotHasKey('render_true', $formrow->getAttributes());
		$this->assertArrayNotHasKey('render_false', $formrow->getAttributes());

		$formrow->render_true = null;
		$formrow->save();
		$formrow = $formrow->fresh();
		$this->assertSame('Sì', $this->render($formrow, true));
		$this->assertSame('Non conforme', $this->render($formrow, false));
	}

	public function test_saving_other_attributes_keeps_existing_overrides() : void
	{
		$formrow = $this->storedFormrow(['render_true' => 'Conforme', 'render_false' => 'Non conforme']);
		$formrow->name = 'Renamed';
		$formrow->save();

		$formrow = $formrow->fresh();
		$this->assertSame('Conforme', $this->render($formrow, true));
		$this->assertSame('Non conforme', $this->render($formrow, false));
	}

	public function test_saving_empty_extra_fields_keeps_following_future_config_changes() : void
	{
		$formrow = $this->storedFormrow();
		$formrow->forceFill(['render_true' => null, 'render_false' => null]);
		$formrow->save();

		config(['filecabinet.formrows.boolean.render_false' => 'Negativo']);
		$this->assertSame('Negativo', $this->render($formrow->fresh(), false));
	}
}
