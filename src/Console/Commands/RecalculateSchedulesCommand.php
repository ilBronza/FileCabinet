<?php

namespace IlBronza\FileCabinet\Console\Commands;

use Carbon\Carbon;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\Schedules\Helpers\Applicators\ScheduleApplicatorHelper;
use IlBronza\Schedules\Models\Type;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Throwable;

class RecalculateSchedulesCommand extends Command
{
	protected $signature = 'filecabinet:schedules:recalculate
		{--dry-run : Report missing schedules without creating them}
		{--chunk=500 : Number of Dossierrows processed at a time}';

	protected $description = 'Create missing schedules for expirationDate FileCabinet rows';

	protected array $summary = [];

	public function handle() : int
	{
		$this->resetSummary();
		$this->getChunkSize();

		$formrowClass = Formrow::getProjectClassName();
		$this->summary['formrows'] = $formrowClass::query()
			->where('type', 'expirationDate')
			->count();

		$formrowClass::query()
			->where('type', 'expirationDate')
			->with('form')
			->orderBy($this->qualifyColumn($formrowClass, 'id'))
			->each(fn (Formrow $formrow) => $this->processFormrow($formrow));

		$this->renderSummary();

		return self::SUCCESS;
	}

	protected function resetSummary() : void
	{
		$this->summary = [
			'formrows' => 0,
			'invalid_formrows' => 0,
			'valued_dossierrows' => 0,
			'already_scheduled' => 0,
			'missing_schedules' => 0,
			'created_schedules' => 0,
			'errors' => 0,
		];
	}

	protected function processFormrow(Formrow $formrow) : void
	{
		$allRows = $this->dossierrowsQuery($formrow);
		$valuedRows = $this->valuedDossierrowsQuery($formrow);
		$details = [
			'total_dossierrows' => (clone $allRows)->count(),
			'valued_dossierrows' => (clone $valuedRows)->count(),
			'already_scheduled' => null,
			'missing_schedules' => null,
			'created_schedules' => 0,
			'errors' => 0,
		];
		$this->summary['valued_dossierrows'] += $details['valued_dossierrows'];

		$scheduleTypeId = $formrow->getSpecialParameter('schedule_type');
		$expirationType = $formrow->getSpecialParameter('expiration_type');
		$scheduleTypeClass = Type::getProjectClassName();
		$scheduleType = $scheduleTypeId === null || $scheduleTypeId === ''
			? null
			: $scheduleTypeClass::query()->find($scheduleTypeId);

		if (! $scheduleType || ! in_array($expirationType, ['start', 'end'], true))
		{
			$this->summary['invalid_formrows']++;
			$this->warn(sprintf(
				'Formrow [%s] has invalid configuration: schedule_type [%s], expiration_type [%s].',
				$formrow->getKey(),
				$this->displayValue($scheduleTypeId),
				$this->displayValue($expirationType)
			));
			$this->renderFormrowDetails(
				$formrow,
				$scheduleTypeId,
				$expirationType,
				$details
			);

			return;
		}

		$details['already_scheduled'] = (clone $valuedRows)
			->whereHas('schedules', fn (Builder $query) => $this->currentScheduleOfType($query, $scheduleType))
			->count();
		$this->summary['already_scheduled'] += $details['already_scheduled'];

		$missingRows = (clone $valuedRows)
			->whereDoesntHave('schedules', fn (Builder $query) => $this->currentScheduleOfType($query, $scheduleType));
		$details['missing_schedules'] = (clone $missingRows)->count();
		$this->summary['missing_schedules'] += $details['missing_schedules'];

		$missingRows
			->chunkById(
				$this->getChunkSize(),
				function (Collection $dossierrows) use (
					$formrow,
					$scheduleType,
					$expirationType,
					&$details
				) : void
				{
					$this->processChunk(
						$dossierrows,
						$formrow,
						$scheduleType,
						$expirationType,
						$details
					);
				}
			);

		$this->renderFormrowDetails(
			$formrow,
			$scheduleType,
			$expirationType,
			$details
		);
	}

	protected function dossierrowsQuery(Formrow $formrow) : Builder
	{
		$dossierrowClass = Dossierrow::getProjectClassName();

		return $dossierrowClass::query()
			->where('formrow_id', $formrow->getKey());
	}

	protected function valuedDossierrowsQuery(Formrow $formrow) : Builder
	{
		return $this->dossierrowsQuery($formrow)
			->whereNotNull('timestamp');
	}

	protected function currentScheduleOfType(Builder $query, Type $scheduleType) : void
	{
		$query
			->where('type_id', $scheduleType->getKey())
			->whereNull('expired_at')
			->whereNull('managed_at');
	}

	protected function processChunk(
		Collection $dossierrows,
		Formrow $formrow,
		Type $scheduleType,
		string $expirationType,
		array &$details
	) : void
	{
		foreach ($dossierrows as $dossierrow)
		{
			$outcome = $this->processDossierrow($dossierrow, $formrow, $scheduleType, $expirationType);

			if ($outcome === 'created')
				$details['created_schedules']++;
			elseif ($outcome === 'already_scheduled')
				$details['already_scheduled']++;
			elseif ($outcome === 'error')
				$details['errors']++;
		}
	}

	protected function processDossierrow(
		Dossierrow $dossierrow,
		Formrow $formrow,
		Type $scheduleType,
		string $expirationType
	) : string
	{
		try
		{
			$value = Carbon::parse($dossierrow->getRawOriginal('timestamp'));

			if ($this->option('dry-run'))
				return 'dry_run';

			$schedule = $expirationType === 'end'
				? ScheduleApplicatorHelper::findOrApplicateEndingScheduleToModel(
					$scheduleType,
					$dossierrow,
					$value
				)
				: ScheduleApplicatorHelper::findOrApplicateStartingScheduleToModel(
					$scheduleType,
					$dossierrow,
					$value
				);

			if ($schedule->wasRecentlyCreated)
			{
				$this->summary['created_schedules']++;

				return 'created';
			}
			else
			{
				$this->summary['already_scheduled']++;

				return 'already_scheduled';
			}
		}
		catch (Throwable $exception)
		{
			$this->summary['errors']++;
			$this->error(sprintf(
				'Formrow [%s], Dossierrow [%s], schedule_type [%s]: %s',
				$formrow->getKey(),
				$dossierrow->getKey(),
				$scheduleType->getKey(),
				$exception->getMessage()
			));

			return 'error';
		}
	}

	protected function renderFormrowDetails(
		Formrow $formrow,
		Type|string|null $scheduleType,
		mixed $expirationType,
		array $details
	) : void
	{
		$form = $formrow->getRelation('form');
		$scheduleTypeId = $scheduleType instanceof Type
			? $scheduleType->getKey()
			: $scheduleType;
		$scheduleTypeName = $scheduleType instanceof Type
			? $scheduleType->getName()
			: '<invalid>';

		$this->newLine();
		$this->info(sprintf(
			'Formrow: %s [%s]',
			$formrow->getName(),
			$formrow->getKey()
		));
		$this->line(sprintf(
			'  Form: %s [%s]',
			$form?->getName() ?? '<missing>',
			$form?->getKey() ?? $formrow->getFormId()
		));
		$this->line(sprintf(
			'  Configuration: schedule_type %s [%s], expiration_type [%s]',
			$scheduleTypeName,
			$this->displayValue($scheduleTypeId),
			$this->displayValue($expirationType)
		));
		$this->line('  Dossierrow total: '.$details['total_dossierrows']);
		$this->line('  Dossierrow with a populated date: '.$details['valued_dossierrows']);
		$this->line('  Dossierrow already having the correct Schedule: '.$this->displayCount($details['already_scheduled']));
		$this->line('  Dossierrow missing the correct Schedule before execution: '.$this->displayCount($details['missing_schedules']));
		$this->line('  Schedules created: '.$details['created_schedules']);
		$this->line('  Errors: '.$details['errors']);
	}

	protected function getChunkSize() : int
	{
		$chunkSize = (int) $this->option('chunk');

		if ($chunkSize < 1)
			throw new InvalidArgumentException('The chunk option must be greater than zero');

		return $chunkSize;
	}

	protected function renderSummary() : void
	{
		$this->newLine();
		$this->info($this->option('dry-run') ? 'Dry-run summary' : 'Recalculation summary');

		foreach ([
			'Formrow expirationDate found' => $this->summary['formrows'],
			'Formrow with invalid configuration' => $this->summary['invalid_formrows'],
			'Dossierrow with a populated date' => $this->summary['valued_dossierrows'],
			'Dossierrow already having the correct Schedule' => $this->summary['already_scheduled'],
			'Dossierrow missing the correct Schedule before execution' => $this->summary['missing_schedules'],
			'Schedules created' => $this->summary['created_schedules'],
			'Errors' => $this->summary['errors'],
		] as $label => $total)
			$this->line("{$label}: {$total}");
	}

	protected function displayValue(mixed $value) : string
	{
		if ($value === null || $value === '')
			return '<missing>';

		return (string) $value;
	}

	protected function displayCount(?int $value) : string
	{
		return $value === null ? '<not available>' : (string) $value;
	}

	protected function qualifyColumn(string $modelClass, string $column) : string
	{
		return (new $modelClass())->qualifyColumn($column);
	}
}
