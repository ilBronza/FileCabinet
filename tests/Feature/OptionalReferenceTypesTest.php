<?php

namespace IlBronza\FileCabinet\Tests\Feature;

use IlBronza\Clients\Models\Client;
use IlBronza\Clients\Models\Destination;
use IlBronza\FileCabinet\FileCabinetServiceProvider;
use IlBronza\FileCabinet\Helpers\DossierrowFormFieldHelper;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\FileCabinet\Providers\RowTypes\RowTypeRegistry;
use IlBronza\FileCabinet\Tests\TestCase;
use IlBronza\Operators\Models\ClientOperator;
use IlBronza\Operators\Models\Contracttype;
use IlBronza\Operators\Models\Operator;
use IlBronza\Operators\OperatorsServiceProvider;
use IlBronza\Warehouse\Models\Room\Room;
use IlBronza\Warehouse\Models\Room\RoomType;
use IlBronza\Warehouse\WarehouseServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class OptionalReferenceTypesTest extends TestCase
{
	private const A = 'a1000000-0000-4000-8000-000000000001';
	private const B = 'a1000000-0000-4000-8000-000000000002';
	private const OLD = 'a1000000-0000-4000-8000-000000000003';
	private const FOREIGN = 'a1000000-0000-4000-8000-000000000004';
	private const ARCHIVED = 'a1000000-0000-4000-8000-000000000005';

	protected function defineEnvironment($app) : void
	{
		parent::defineEnvironment($app);
		foreach (['operators' => 'Operators', 'warehouse' => 'Warehouse', 'clients' => 'Clients'] as $key => $package)
		{
			$file = dirname(__DIR__, 3) . "/{$package}/config/{$key}.php";
			if (is_file($file))
				$app['config']->set($key, require $file);
		}
		$app['config']->set('operators.models.clientOperator.class', ReferenceClientOperator::class);
		$app['config']->set('operators.models.clientOperator.table', 'reference_workers');
		$app['config']->set('operators.models.operator.class', ReferenceOperator::class);
		$app['config']->set('operators.models.operator.table', 'reference_operators');
		$app['config']->set('operators.models.contracttype.class', ReferenceContracttype::class);
		$app['config']->set('operators.models.contracttype.table', 'reference_contracttypes');
		$app['config']->set('warehouse.models.roomType.class', ReferenceRoomType::class);
		$app['config']->set('warehouse.models.roomType.table', 'reference_room_types');
		$app['config']->set('warehouse.models.room.class', ReferenceRoom::class);
		$app['config']->set('warehouse.models.room.table', 'reference_rooms');
		$app['config']->set('clients.models.destination.class', ReferenceDestination::class);
		$app['config']->set('clients.models.destination.table', 'reference_destinations');
	}

	protected function setUp() : void
	{
		parent::setUp();
		if (! class_exists(OperatorsServiceProvider::class) || ! class_exists(WarehouseServiceProvider::class))
			$this->markTestSkipped('Install Operators and Warehouse to run the optional reference integration tests.');

		// Isolate the integration hook from an unrelated legacy controller constructed by Operators.
		$this->app->instance('IlBronza\\Operators\\Http\\Controllers\\CrudOperatorsController', new \stdClass());
		(new FileCabinetServiceProvider($this->app))->register();
		(new OperatorsServiceProvider($this->app))->register();
		(new WarehouseServiceProvider($this->app))->register();
		foreach (['filecabinet' => 'FileCabinet', 'operators' => 'Operators', 'warehouse' => 'Warehouse'] as $key => $package)
			app('translator')->addNamespace($key, dirname(__DIR__, 3) . "/{$package}/resources/lang");
		app()->setLocale('it');

		Schema::create('reference_operators', function (Blueprint $table) {
			$table->string('id')->primary(); $table->string('display_name'); $table->softDeletes();
		});
		Schema::create('reference_workers', function (Blueprint $table) {
			$table->string('id')->primary(); $table->string('client_id'); $table->string('operator_id');
			$table->date('started_at')->nullable(); $table->date('ended_at')->nullable(); $table->softDeletes();
		});
		Schema::create('reference_destinations', function (Blueprint $table) {
			$table->string('id')->primary(); $table->string('client_id'); $table->softDeletes();
		});
		Schema::create('reference_rooms', function (Blueprint $table) {
			$table->string('id')->primary(); $table->string('name'); $table->string('destination_id')->nullable();
			$table->softDeletes();
		});
		foreach (['reference_contracttypes', 'reference_room_types'] as $name)
			Schema::create($name, function (Blueprint $table) {
				$table->string('id')->primary(); $table->string('name');
				$table->boolean('visible')->default(true); $table->timestamp('archived_at')->nullable();
				$table->softDeletes();
			});
		DB::table('reference_destinations')->insert([
			['id' => 'dest-one', 'client_id' => 'company-one'], ['id' => 'dest-two', 'client_id' => 'company-two'],
		]);
		foreach ([self::A, self::B, self::OLD, self::FOREIGN, self::ARCHIVED] as $id)
		{
			DB::table('reference_contracttypes')->insert([
				'id' => $id, 'name' => 'Contracttype ' . $id, 'visible' => $id !== self::FOREIGN,
				'archived_at' => $id === self::OLD ? '2000-01-01' : null,
				'deleted_at' => $id === self::ARCHIVED ? '2000-01-01' : null,
			]);
			DB::table('reference_room_types')->insert([
				'id' => $id, 'name' => 'Room type ' . $id, 'visible' => $id !== self::FOREIGN,
				'deleted_at' => in_array($id, [self::OLD, self::ARCHIVED], true) ? '2000-01-01' : null,
			]);
			DB::table('reference_operators')->insert(['id' => 'operator-' . $id, 'display_name' => 'Worker ' . $id]);
			DB::table('reference_workers')->insert([
				'id' => $id, 'client_id' => $id === self::FOREIGN ? 'company-two' : 'company-one',
				'operator_id' => 'operator-' . $id, 'ended_at' => $id === self::OLD ? '2000-01-01' : null,
				'deleted_at' => $id === self::ARCHIVED ? '2000-01-01' : null,
			]);
			DB::table('reference_rooms')->insert([
				'id' => $id, 'name' => 'Room ' . $id, 'destination_id' => $id === self::FOREIGN ? 'dest-two' : 'dest-one',
				'deleted_at' => in_array($id, [self::OLD, self::ARCHIVED], true) ? '2000-01-01' : null,
			]);
		}
	}

	private function row(string $type, string $company = 'company-one') : Dossierrow
	{
		$formrow = (new Formrow())->forceFill(['id' => 'form-' . $type, 'type' => $type, 'required' => false, 'name' => $type]);
		$owner = (new Client())->forceFill(['id' => $company]);
		$dossier = (new Dossier())->setRelation('dossierable', $owner);
		$id = $type . '-' . $company;
		DB::table(config('filecabinet.models.dossierrow.table'))->insert(['id' => $id, 'formrow_id' => $formrow->getKey()]);
		return Dossierrow::findOrFail($id)->setRelation('formrow', $formrow)->setRelation('dossier', $dossier);
	}

	private function rulesPass(Dossierrow $row, mixed $value) : bool
	{
		return validator(['value' => $value], ['value' => $row->getFormfieldRules()])->passes();
	}

	public function test_single_and_multiple_variants_are_distinct_classes_files_and_identifiers() : void
	{
		$registry = app(RowTypeRegistry::class);
		$paths = [];
		foreach (['clientOperator', 'clientOperators', 'room', 'rooms', 'contracttype', 'roomType'] as $type)
		{
			$row = $registry->make($type);
			$this->assertTrue($row->isAvailable());
			$this->assertSame($type, $row::getType());
			$this->assertArrayHasKey($type, $registry->choices());
			$paths[] = (new \ReflectionClass($row))->getFileName();
		}
		$this->assertCount(6, array_unique($paths));
	}

	public function test_single_fields_store_uuid_ids_and_render_worker_and_room_names() : void
	{
		foreach (['clientOperator' => 'Worker ', 'room' => 'Room ', 'contracttype' => 'Contracttype ', 'roomType' => 'Room type '] as $type => $label)
		{
			$row = $this->row($type);
			$this->assertTrue($row->storeRowValue(self::A));
			$this->assertSame(self::A, $row->getValue());
			$this->assertSame(self::A, $row->fresh()->string);
			$this->assertSame($label . self::A, $row->getShowValue());
			$this->assertSame($label . self::A, $row->renderFormfieldValue());
			$this->assertFalse($row->isFormfieldMultiple());
			$field = DossierrowFormFieldHelper::createFieldFromDossierrow($row);
			$this->assertSame($label . self::A, $field->getPossibleValuesArray()[self::A]);
			$this->assertSame($label . self::A, $field->getShowValue());
			$this->assertSame('renderValueByFormrow', $row->getRowType()->getDatatableValueMethod());
		}
	}

	public function test_multiple_fields_store_json_arrays_and_reject_nested_arrays_numbers_and_foreign_ids() : void
	{
		foreach (['clientOperators', 'rooms'] as $type)
		{
			$row = $this->row($type);
			$ids = [self::A, self::B];
			$this->assertTrue($row->storeRowValue($ids));
			$this->assertSame($ids, json_decode($row->fresh()->text, true));
			$this->assertSame($ids, $row->getValue());
			$this->assertTrue($row->isFormfieldMultiple());
			$field = DossierrowFormFieldHelper::createFieldFromDossierrow($row);
			$this->assertSame($row->renderFormfieldValue(), $field->getShowValue());
			foreach ([[self::A, self::FOREIGN], [[self::A]], [123], ['key' => self::A], self::A] as $invalid)
				$this->assertFalse($this->rulesPass($row, $invalid));
			$this->assertTrue($row->storeRowValue([]));
			$this->assertSame('[]', $row->fresh()->text);
			$this->assertTrue($row->storeRowValue(null));
			$this->assertNull($row->fresh()->text);
		}
	}

	public function test_choices_and_server_validation_are_scoped_to_the_dossier_company() : void
	{
		foreach (['clientOperator', 'room'] as $type)
		{
			$one = $this->row($type);
			$two = $this->row($type, 'company-two');
			$this->assertSame([self::A, self::B], array_keys($one->getRowType()->getPossibleValuesArray()));
			$this->assertSame([self::FOREIGN], array_keys($two->getRowType()->getPossibleValuesArray()));
			$this->assertFalse($this->rulesPass($one, self::FOREIGN));
			$this->assertFalse($this->rulesPass($two, self::A));
			$this->assertFalse($this->rulesPass($one, 'operator-' . self::A));
			try {
				$one->storeRowValue(self::FOREIGN, false);
				$this->fail('Direct writes must also validate ownership.');
			} catch (ValidationException $e) {
				$this->assertNull($one->fresh()->string);
			}
			try {
				$one->setValue(self::FOREIGN);
				$this->fail('Direct setters must also validate ownership.');
			} catch (ValidationException $e) {
				$this->assertNull($one->string);
			}
		}
	}

	public function test_historical_references_remain_readable_and_can_only_be_retained_in_their_original_field() : void
	{
		foreach (['clientOperator', 'room', 'clientOperators', 'rooms', 'contracttype', 'roomType'] as $type)
		{
			$row = $this->row($type);
			$multiple = $row->isFormfieldMultiple();
			$value = $multiple ? [self::OLD, self::ARCHIVED] : self::OLD;
			$column = $row->getDatabaseField();
			DB::table($row->getTable())->where('id', $row->getKey())->update([$column => $multiple ? json_encode($value) : $value]);
			$row->setRawAttributes($row->fresh()->getAttributes(), true);
			$this->assertNotEmpty($row->getShowValue());
			$this->assertTrue($this->rulesPass($row, $value));
			$this->assertStringContainsString('storico', $row->getRowType()->getPossibleValuesArray()[self::OLD]);
			$this->assertTrue($row->storeRowValue($value));
			$this->assertSame($value, $row->getValue());
			$this->assertTrue($row->storeRowValue($multiple ? [self::A] : self::A));
			$this->assertArrayNotHasKey(self::OLD, $row->getRowType()->getPossibleValuesArray());
			$this->assertFalse($this->rulesPass($row, $value));
		}
	}

	public function test_historical_ids_from_another_company_are_rejected_even_if_already_stored() : void
	{
		foreach (['clientOperator', 'room'] as $type)
		{
			$row = $this->row($type);
			DB::table($row->getTable())->where('id', $row->getKey())->update(['string' => self::FOREIGN]);
			$row->setRawAttributes($row->fresh()->getAttributes(), true);
			$this->assertFalse($this->rulesPass($row, self::FOREIGN));
			$this->assertSame(self::FOREIGN, $row->getShowValue());
		}
	}

	public function test_catalog_choices_are_shared_between_owners_and_reject_invalid_or_archived_new_references() : void
	{
		foreach (['contracttype', 'roomType'] as $type)
		{
			$one = $this->row($type);
			$two = $this->row($type, 'company-two');
			$this->assertSame([self::A, self::B], array_keys($one->getRowType()->getPossibleValuesArray()));
			$this->assertSame($one->getRowType()->getPossibleValuesArray(), $two->getRowType()->getPossibleValuesArray());
			$two->getDossier()->setRelation('dossierable', null);
			$this->assertTrue($this->rulesPass($two, self::A));
			$this->assertTrue($one->storeRowValue(self::A));
			foreach ([self::OLD, self::ARCHIVED, self::FOREIGN, 'missing-uuid', 123, [self::A]] as $invalid)
			{
				$this->assertFalse($this->rulesPass($one, $invalid));
				try {
					$one->storeRowValue($invalid, false);
					$this->fail('Catalog writes must validate references even without optional validation.');
				} catch (ValidationException $e) {
					$this->assertSame(self::A, $one->fresh()->string);
				}
			}
		}
	}

	public function test_catalog_historical_queries_preserve_the_host_application_scopes() : void
	{
		foreach (['contracttype', 'roomType'] as $type)
		{
			$row = $this->row($type);
			DB::table($row->getTable())->where('id', $row->getKey())->update(['string' => self::FOREIGN]);
			$row->setRawAttributes($row->fresh()->getAttributes(), true);
			$this->assertFalse($this->rulesPass($row, self::FOREIGN));
			$this->assertSame(self::FOREIGN, $row->getShowValue());
			$this->assertStringNotContainsString('Contracttype ', $row->renderFormfieldValue());
			$this->assertStringNotContainsString('Room type ', $row->renderFormfieldValue());
		}
	}

	public function test_future_workers_and_rooms_without_a_company_cannot_be_selected() : void
	{
		DB::table('reference_workers')->where('id', self::A)->update(['started_at' => '2999-01-01']);
		DB::table('reference_rooms')->where('id', self::A)->update(['destination_id' => null]);
		foreach (['clientOperator', 'room'] as $type)
		{
			$row = $this->row($type);
			$this->assertArrayNotHasKey(self::A, $row->getRowType()->getPossibleValuesArray());
			$this->assertFalse($this->rulesPass($row, self::A));
			$row->getDossier()->setRelation('dossierable', new Client());
			$this->assertSame([], $row->getRowType()->getPossibleValuesArray());
			$this->assertFalse($this->rulesPass($row, self::B));
		}
	}

	public function test_real_optional_types_restore_single_and_multiple_values_without_conversion() : void
	{
		$installed = app(RowTypeRegistry::class);
		foreach (['clientOperator', 'clientOperators', 'room', 'rooms', 'contracttype', 'roomType'] as $type)
		{
			$row = $this->row($type);
			$value = $row->isFormfieldMultiple() ? [self::A, self::B] : self::A;
			$row->storeRowValue($value);
			$before = $row->fresh()->getAttributes();
			$this->app->instance(RowTypeRegistry::class, new RowTypeRegistry());
			$this->assertFalse($row->getRowType()->isAvailable());
			$this->assertFalse($row->storeRowValue('attempted overwrite'));
			$this->assertFalse($row->emptyRowValue());
			$this->assertSame($before, $row->fresh()->getAttributes());
			$this->app->instance(RowTypeRegistry::class, $installed);
			$this->assertSame($value, $row->getValue());
		}
	}

	public function test_required_reference_fields_reject_empty_values() : void
	{
		foreach (['clientOperator', 'clientOperators', 'room', 'rooms', 'contracttype', 'roomType'] as $type)
		{
			$row = $this->row($type);
			$row->getFormrow()->required = true;
			$this->assertFalse($this->rulesPass($row, null));
			$this->assertFalse($this->rulesPass($row, $row->isFormfieldMultiple() ? [] : ''));
			$this->assertTrue($this->rulesPass($row, $row->isFormfieldMultiple() ? [self::A] : self::A));
		}
	}

	public function test_archived_destinations_allow_retaining_existing_rooms_but_no_new_choices() : void
	{
		$row = $this->row('room');
		$row->storeRowValue(self::A);
		DB::table('reference_destinations')->where('id', 'dest-one')->update(['deleted_at' => '2000-01-01']);
		$this->assertTrue($this->rulesPass($row, self::A));
		$this->assertFalse($this->rulesPass($row, self::B));
		$this->assertSame([self::A], array_keys($row->getRowType()->getPossibleValuesArray()));
		$this->assertSame('Room ' . self::A, $row->getShowValue());
	}
}

// Conditional declarations keep FileCabinet's suite runnable without optional packages.
if (class_exists(ClientOperator::class))
{
	class ReferenceClientOperator extends ClientOperator {}
	class ReferenceOperator extends Operator
	{
		protected $with = [];
		public function getName() : ?string { return $this->display_name; }
	}
}
if (class_exists(Room::class))
{
	class ReferenceRoom extends Room {}
}
if (class_exists(Contracttype::class))
{
	class ReferenceContracttype extends Contracttype
	{
		protected static function booted() : void
		{
			parent::booted();
			static::addGlobalScope('visible-catalog', fn ($query) => $query->where('visible', true));
		}
	}
}
if (class_exists(RoomType::class))
{
	class ReferenceRoomType extends RoomType
	{
		protected static function booted()
		{
			parent::booted();
			static::addGlobalScope('visible-catalog', fn ($query) => $query->where('visible', true));
		}
	}
}
if (class_exists(Destination::class))
{
	class ReferenceDestination extends Destination {}
}
