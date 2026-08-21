<?php

namespace IlBronza\FileCabinet\Tests\Feature;

use Carbon\Carbon;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\FileCabinet\Providers\RowTypes\Rows\FormrowExpirationDate;
use IlBronza\FileCabinet\Tests\TestCase;
use IlBronza\MeasurementUnits\Models\MeasurementUnit;
use IlBronza\Schedules\Helpers\Applicators\ScheduleApplicatorHelper;
use IlBronza\Schedules\Models\Schedule;
use IlBronza\Schedules\Models\Type;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class RecalculateSchedulesCommandTest extends TestCase
{
	protected int $fixtureSequence = 0;

	protected function setUp() : void
	{
		parent::setUp();

		MeasurementUnit::query()->forceCreate([
			'id' => 'days',
			'name' => 'Days',
			'base_measurement_unit' => 'Day',
			'proportion_toward_base_measurement_unit' => 1,
		]);

		DB::table(config('filecabinet.models.form.table'))->insert([
			'id' => 'form',
			'name' => 'Fixture Form',
			'created_at' => now(),
			'updated_at' => now(),
		]);
	}

	public function testItDiscoversAllExpirationRowsExcludesOtherTypesAndHandlesMultipleScheduleTypes() : void
	{
		$renewal = $this->createType('renewal', 10);
		$inspection = $this->createType('inspection', 20);
		$startingFormrow = $this->createFormrow('expirationDate', $renewal, 'start');
		$endingFormrow = $this->createFormrow('expirationDate', $inspection, 'end');
		$plainDateFormrow = $this->createFormrow('date');
		$startingRow = $this->createDossierrow($startingFormrow, '2026-08-01 00:00:00');
		$endingRow = $this->createDossierrow($endingFormrow, '2026-09-01 00:00:00');
		$this->createDossierrow($plainDateFormrow, '2026-10-01 00:00:00');

		$this->assertSame(0, Artisan::call('filecabinet:schedules:recalculate', ['--chunk' => 1]));
		$output = Artisan::output();
		$this->assertSummaryContains($output, [
			'Formrow expirationDate found' => 2,
			'Formrow with invalid configuration' => 0,
			'Dossierrow with a populated date' => 2,
			'Dossierrow already having the correct Schedule' => 0,
			'Schedules created' => 2,
			'Errors' => 0,
		]);
		$this->assertStringContainsString(
			'Formrow: '.$startingFormrow->getName().' ['.$startingFormrow->getKey().']',
			$output
		);
		$this->assertStringContainsString('Form: Fixture Form [form]', $output);
		$this->assertStringContainsString(
			'Dossierrow missing the correct Schedule before execution: 1',
			$output
		);

		$this->assertSame(2, Schedule::query()->count());
		$this->assertDatabaseHas(config('schedules.models.schedule.table'), [
			'type_id' => 'renewal',
			'schedulable_id' => $startingRow->getKey(),
		]);
		$this->assertDatabaseHas(config('schedules.models.schedule.table'), [
			'type_id' => 'inspection',
			'schedulable_id' => $endingRow->getKey(),
		]);
		$this->assertDatabaseMissing(config('schedules.models.schedule.table'), [
			'schedulable_id' => Dossierrow::query()->where('formrow_id', $plainDateFormrow->getKey())->value('id'),
		]);

		$startingSchedule = Schedule::query()->where('type_id', 'renewal')->firstOrFail();
		$endingSchedule = Schedule::query()->where('type_id', 'inspection')->firstOrFail();
		$this->assertSame('2026-08-01', $startingSchedule->getStartingValue()->toDateString());
		$this->assertSame('2026-08-11', $startingSchedule->getDeadlineValue()->toDateString());
		$this->assertSame('2026-08-12', $endingSchedule->getStartingValue()->toDateString());
		$this->assertSame('2026-09-01', $endingSchedule->getDeadlineValue()->toDateString());
	}

	public function testItExcludesEmptyDatesAndCreatesConfiguredScheduleWhenAnotherTypeExists() : void
	{
		$requiredType = $this->createType('required', 10);
		$otherType = $this->createType('other', 5);
		$formrow = $this->createFormrow('expirationDate', $requiredType, 'start');
		$emptyRow = $this->createDossierrow($formrow, null);
		$valuedRow = $this->createDossierrow($formrow, '2026-08-02 00:00:00');

		ScheduleApplicatorHelper::findOrApplicateStartingScheduleToModel(
			$otherType,
			$valuedRow,
			Carbon::parse('2026-07-01')
		);

		$this->assertSame(0, Artisan::call('filecabinet:schedules:recalculate'));

		$this->assertSame(2, Schedule::query()->count());
		$this->assertDatabaseHas(config('schedules.models.schedule.table'), [
			'type_id' => 'required',
			'schedulable_id' => $valuedRow->getKey(),
		]);
		$this->assertDatabaseMissing(config('schedules.models.schedule.table'), [
			'type_id' => 'required',
			'schedulable_id' => $emptyRow->getKey(),
		]);
	}

	public function testItIsIdempotentAndCountsTheExistingCorrectSchedule() : void
	{
		$type = $this->createType('renewal', 10);
		$formrow = $this->createFormrow('expirationDate', $type, 'start');
		$this->createDossierrow($formrow, '2026-08-03 00:00:00');

		$this->assertSame(0, Artisan::call('filecabinet:schedules:recalculate'));
		$firstScheduleId = Schedule::query()->sole()->getKey();

		$this->assertSame(0, Artisan::call('filecabinet:schedules:recalculate'));
		$this->assertSummaryContains(Artisan::output(), [
			'Dossierrow already having the correct Schedule' => 1,
			'Schedules created' => 0,
		]);

		$this->assertSame(1, Schedule::query()->count());
		$this->assertSame($firstScheduleId, Schedule::query()->sole()->getKey());
	}

	public function testDryRunDoesNotCreateSchedulesOrChangeTheDossierrow() : void
	{
		$type = $this->createType('renewal', 10);
		$formrow = $this->createFormrow('expirationDate', $type, 'start');
		$dossierrow = $this->createDossierrow($formrow, '2026-08-04 12:34:56');
		$originalUpdatedAt = $dossierrow->updated_at;

		$this->assertSame(0, Artisan::call('filecabinet:schedules:recalculate', ['--dry-run' => true]));
		$output = Artisan::output();
		$this->assertStringContainsString('Dry-run summary', $output);
		$this->assertSummaryContains($output, [
			'Dossierrow with a populated date' => 1,
			'Schedules created' => 0,
			'Errors' => 0,
		]);

		$this->assertSame(0, Schedule::query()->count());
		$this->assertSame('2026-08-04 12:34:56', $dossierrow->fresh()->getRawOriginal('timestamp'));
		$this->assertEquals($originalUpdatedAt, $dossierrow->fresh()->updated_at);
	}

	public function testInvalidConfigurationsAreReportedAndSkipped() : void
	{
		$type = $this->createType('renewal', 10);
		$missingType = $this->createFormrow('expirationDate', null, 'start');
		$unknownType = $this->createFormrow('expirationDate', null, 'end', 'unknown');
		$invalidDirection = $this->createFormrow('expirationDate', $type, 'sideways');
		$this->createDossierrow($missingType, '2026-08-05 00:00:00');
		$this->createDossierrow($unknownType, '2026-08-06 00:00:00');
		$this->createDossierrow($invalidDirection, '2026-08-07 00:00:00');

		$this->assertSame(0, Artisan::call('filecabinet:schedules:recalculate'));
		$output = Artisan::output();
		$this->assertStringContainsString('invalid configuration', $output);
		$this->assertStringContainsString('unknown', $output);
		$this->assertSummaryContains($output, [
			'Formrow expirationDate found' => 3,
			'Formrow with invalid configuration' => 3,
			'Schedules created' => 0,
			'Errors' => 0,
		]);

		$this->assertSame(0, Schedule::query()->count());
	}

	public function testItContinuesAfterOneDossierrowFails() : void
	{
		$type = $this->createType('renewal', 10);
		$formrow = $this->createFormrow('expirationDate', $type, 'start');
		$invalid = $this->createDossierrow($formrow, 'not-a-date');
		$valid = $this->createDossierrow($formrow, '2026-08-08 00:00:00');

		$this->assertSame(0, Artisan::call('filecabinet:schedules:recalculate', ['--chunk' => 1]));
		$output = Artisan::output();
		$this->assertStringContainsString('Dossierrow ['.$invalid->getKey().']', $output);
		$this->assertSummaryContains($output, [
			'Dossierrow with a populated date' => 2,
			'Schedules created' => 1,
			'Errors' => 1,
		]);

		$this->assertSame(1, Schedule::query()->count());
		$this->assertSame($valid->getKey(), Schedule::query()->sole()->schedulable_id);
	}

	public function testExpirationRowThrowsContextualErrorForMissingOrUnknownScheduleType() : void
	{
		$formrow = $this->createFormrow('expirationDate', null, 'start');
		$dossierrow = $this->createDossierrow($formrow, '2026-08-09 00:00:00');
		$rowType = new FormrowExpirationDate();
		$rowType->setModel($formrow);

		try
		{
			$rowType->performAfterStoreAction($dossierrow, Carbon::parse('2026-08-09'));
			$this->fail('Missing schedule_type did not throw an exception.');
		}
		catch (RuntimeException $exception)
		{
			$this->assertStringContainsString('schedule_type [<missing>]', $exception->getMessage());
			$this->assertStringContainsString('Formrow ['.$formrow->getKey().']', $exception->getMessage());
			$this->assertStringContainsString('Dossierrow ['.$dossierrow->getKey().']', $exception->getMessage());
		}

		$formrow->parameters = ['schedule_type' => 'unknown', 'expiration_type' => 'start'];
		$rowType->setModel($formrow);

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('schedule_type [unknown]');
		$this->expectExceptionMessage('Formrow ['.$formrow->getKey().']');
		$this->expectExceptionMessage('Dossierrow ['.$dossierrow->getKey().']');

		$rowType->performAfterStoreAction($dossierrow, Carbon::parse('2026-08-09'));
	}

	protected function createType(string $id, int $validity) : Type
	{
		DB::table(config('schedules.models.type.table'))->insert([
			'id' => $id,
			'name' => Str::headline($id),
			'validity' => $validity,
			'measurement_unit_id' => 'days',
			'allow_multiple' => false,
			'roles' => json_encode([]),
			'models' => json_encode([[
				'model' => Dossierrow::class,
				'source' => 'timestamp',
			]]),
			'created_at' => now(),
			'updated_at' => now(),
		]);

		return Type::query()->findOrFail($id);
	}

	protected function createFormrow(
		string $type,
		?Type $scheduleType = null,
		?string $expirationType = null,
		?string $scheduleTypeId = null
	) : Formrow
	{
		$id = $this->nextFixtureId('formrow');
		$parameters = array_filter([
			'schedule_type' => $scheduleTypeId ?? $scheduleType?->getKey(),
			'expiration_type' => $expirationType,
		], fn (mixed $value) : bool => $value !== null);

		DB::table(config('filecabinet.models.formrow.table'))->insert([
			'id' => $id,
			'form_id' => 'form',
			'name' => 'Row '.$id,
			'slug' => 'row-'.$id,
			'type' => $type,
			'parameters' => json_encode($parameters),
			'created_at' => now(),
			'updated_at' => now(),
		]);

		return Formrow::query()->findOrFail($id);
	}

	protected function createDossierrow(Formrow $formrow, ?string $timestamp) : Dossierrow
	{
		$id = $this->nextFixtureId('dossierrow');

		DB::table(config('filecabinet.models.dossierrow.table'))->insert([
			'id' => $id,
			'dossier_id' => 'dossier',
			'formrow_id' => $formrow->getKey(),
			'timestamp' => $timestamp,
			'created_at' => now(),
			'updated_at' => now(),
		]);

		return Dossierrow::query()->findOrFail($id);
	}

	protected function nextFixtureId(string $prefix) : string
	{
		$this->fixtureSequence++;

		return sprintf('%s-%04d', $prefix, $this->fixtureSequence);
	}

	protected function assertSummaryContains(string $output, array $expected) : void
	{
		foreach ($expected as $label => $total)
			$this->assertStringContainsString("{$label}: {$total}", $output);
	}
}
