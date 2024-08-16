<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use Carbon\Carbon;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowWithEventTrait;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowWithSpecialParametersInterface;
use IlBronza\FileCabinet\Providers\RowTypes\SpecialParametersTrait;
use IlBronza\FormField\Fields\DateFormField;
use IlBronza\FormField\FormField;
use IlBronza\Schedules\Helpers\Applicators\ScheduleApplicatorHelper;
use IlBronza\Schedules\Models\Type;

use function __;

class FormrowExpirationDate extends BaseRow implements FormrowWithSpecialParametersInterface
{
	use SpecialParametersTrait;
	use FormrowWithEventTrait;

	static $fieldType = 'date';
	static $databaseField = 'timestamp';

	public function getDefaultRules() : array
	{
		return [
			'date',
			// 'after:today'
		];
	}

	public function performBeforeStoreAction(Dossierrow $dossierrow, mixed $value)
	{

	}

	protected function isEndingExpirationDateType() : bool
	{
		return $this->getExpirationType() == 'end';
	}

	public function performAfterStoreAction(Dossierrow $dossierrow, mixed $date)
	{
		$scheduleType = Type::find($this->getScheduleTypeId());

//		$dossier = $dossierrow->getDossier();

		if(! $date instanceof Carbon)
			$date = Carbon::createFromFormat('Y-m-d', $date);

		if($this->isEndingExpirationDateType())
			return ScheduleApplicatorHelper::findOrApplicateEndingScheduleToModel($scheduleType, $dossierrow, $date);

		return ScheduleApplicatorHelper::findOrApplicateStartingScheduleToModel($scheduleType, $dossierrow, $date);
	}

	public function getValidationRulesArrayFromSpecialParametersArray() : array
	{
		return [];
	}

	public function transformValue(mixed $databaseValue) : mixed
	{
		return substr($databaseValue, 0, 10);
	}

	public function getFormField() : FormField
	{
		return new DateFormField();
	}

	protected function getPossibleScheduleTypesArray() : array
	{
		return Type::select('name', 'id')->pluck('name', 'id')->toArray();
	}

	public function getSpecialParametersFieldsetParameters() : array
	{
		return [
			'parameters' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'expiration_type' => [
						'type' => 'select',
						'rules' => 'string|required',
						'list' => [
							'start' => __('schedules::dates.start'),
							'end' => __('schedules::dates.end')
						],
						'value' => $this->getExpirationType()
					],
					'schedule_type' => [
						'type' => 'select',
						'rules' => 'string|required',
						'list' => $this->getPossibleScheduleTypesArray(),
						'value' => $this->getScheduleTypeId()
					]
				]
			]
		];
	}

	/**
	 * @return mixed
	 */
	public function getScheduleTypeId()
	{
		return $this->getModel()->getSpecialParameter('schedule_type', null);
	}

	/**
	 * @return mixed
	 */
	public function getExpirationType()
	{
		return $this->getModel()->getSpecialParameter('expiration_type', null);
	}
}