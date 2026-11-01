<?php

namespace IlBronza\FileCabinet\Tests\Feature;

use IlBronza\FileCabinet\FileCabinetServiceProvider;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowNamesTypeHelper;
use IlBronza\FileCabinet\Providers\RowTypes\RowTypeRegistry;
use IlBronza\FileCabinet\Providers\RowTypes\Rows\FormrowText;
use IlBronza\FileCabinet\Tests\TestCase;

class RowTypeRegistryTest extends TestCase
{
	protected function setUp() : void
	{
		parent::setUp();
		(new FileCabinetServiceProvider($this->app))->register();
		app('translator')->addNamespace('filecabinet', __DIR__ . '/../../resources/lang');
	}

	public function test_all_legacy_identifiers_classes_and_names_are_preserved() : void
	{
		$registry = app(RowTypeRegistry::class);
		$files = glob(__DIR__ . '/../../src/Providers/RowTypes/Rows/Formrow*.php');
		$operatorsPresent = class_exists(\IlBronza\Operators\OperatorsServiceProvider::class);
		$this->assertCount(count($files) - ($operatorsPresent ? 0 : 2), $registry->choices());
		foreach ($files as $file)
		{
			$class = FormrowNamesTypeHelper::getClassFromFilePath($file);
			$id = $class::getType();
			if (! $operatorsPresent && in_array($id, ['operatorSelect', 'operatorsSelect'], true))
			{
				$this->assertFalse($registry->has($id));
				continue;
			}
			$this->assertInstanceOf($class, FormrowNamesTypeHelper::getByType($id));
			$this->assertSame($class::getTranslatedName(), FormrowNamesTypeHelper::getPossibleTypesArray()[$id]);
			$this->assertSame($class::getTranslatedName(), FormrowNamesTypeHelper::getTranslatedNameByType($id));
		}
		$this->assertSame('contract-file', $registry->make('contract-file')::getType());
	}

	public function test_external_registration_supplies_names_metadata_and_fresh_instances() : void
	{
		$registry = app(RowTypeRegistry::class);
		$registry->register('external', ExternalRegistryRow::class, 'ilbronza/example');
		$this->assertSame('Example field', FormrowNamesTypeHelper::getPossibleTypesArray()['external']);
		$this->assertSame('ilbronza/example', $registry->info('external')['package']);
		$this->assertSame('externalRegistryRow', ExternalRegistryRow::getType());

		// A host application cannot accidentally make row objects shared singletons.
		$this->app->singleton(ExternalRegistryRow::class);
		$first = $registry->make('external');
		$first->setDossierrow(new Dossierrow());
		$second = FormrowNamesTypeHelper::getByType('external');
		$this->assertNotSame($first, $second);
		$this->assertNull($second->getDossierrow());
	}

	public function test_form_and_dossier_resolution_never_leaks_context_or_caches_a_removed_type() : void
	{
		$form = (new Formrow())->forceFill(['type' => 'text']);
		$one = (new Dossierrow())->setRelation('formrow', $form);
		$two = (new Dossierrow())->setRelation('formrow', $form);
		$a = $one->getRowType();
		$b = $two->getRowType();
		$this->assertNotSame($a, $b);
		$this->assertSame($one, $a->getDossierrow());
		$this->assertSame($two, $b->getDossierrow());
		$this->assertNull($form->getRowType()->getDossierrow());
		$form->type = 'removed';
		$this->assertFalse($one->getRowType()->isAvailable());
	}

	public function test_duplicate_identifiers_fail_with_an_actionable_error() : void
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('use replace()');
		app(RowTypeRegistry::class)->register('text', ExternalRegistryRow::class);
	}

	public function test_explicit_replacement_wins_in_either_registration_order() : void
	{
		foreach ([true, false] as $replaceFirst)
		{
			$registry = new RowTypeRegistry();
			if ($replaceFirst)
				$registry->replace('external', FormrowText::class);
			$registry->register('external', ExternalRegistryRow::class);
			if (! $replaceFirst)
				$registry->replace('external', FormrowText::class);
			$this->assertSame(FormrowText::class, get_class($registry->make('external')));
		}
	}

	public function test_invalid_or_abstract_classes_are_rejected_at_registration() : void
	{
		$this->expectException(\InvalidArgumentException::class);
		app(RowTypeRegistry::class)->register('bad', \stdClass::class);
	}
}

class ExternalRegistryRow extends FormrowText
{
	public static function getTranslatedName() : string { return 'Example field'; }
}
