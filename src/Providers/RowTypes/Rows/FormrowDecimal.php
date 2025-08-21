<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowWithSpecialParametersInterface;
use IlBronza\FileCabinet\Providers\RowTypes\SpecialParametersTrait;
use IlBronza\FileCabinet\Providers\RowTypes\StandardCheckFieldValidityParametersTrait;
use IlBronza\FormField\Fields\NumberFormField;
use IlBronza\FormField\FormField;

use function array_keys;
use function floatval;
use function implode;
use function number_format;

class FormrowDecimal extends BaseRow implements FormrowWithSpecialParametersInterface
{
	use StandardCheckFieldValidityParametersTrait;

	use SpecialParametersTrait;

	static $valueType = 'numeric';
	static $fieldType = 'number';
	static $databaseField = 'decimal';

	public int $min = - 999999999999;
	public int $max = 999999999999;

	public function getDefaultRules() : array
	{
		return [
			static::$valueType,
			'min:' . $this->min,
			'max:' . $this->max
		];
	}

	public function getFormField() : FormField
	{
		$field = new NumberFormField();

		return new NumberFormField();
	}

	public function transformValue(mixed $databaseValue) : float
	{
		$decimals = $this->getDecimals();

		return floatval(number_format($databaseValue, $decimals, '.', ''));
	}

	public function getDecimals() : ?int
	{
		return $this->getModel()->getSpecialParameter('decimals', null);
	}

	public function getValidationRulesArrayFromSpecialParametersArray() : array
	{
		return [];
	}

	public function getCheckFieldValidityParametersFieldsetParameters() : array
	{
		$limitTypes = [
			'min' => 'Min',
			'max' => 'Max'
		];

		return [
			'checkFieldValidityParameters' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'limits' => [
						'type' => 'json',
						'fields' => [
							'limitType' => [
								'type' => 'select',
								'multiple' => false,
								'select2' => false,
								'rules' => 'string|nullable|in:' . implode(',', array_keys($limitTypes)),
								'possibleValuesArray' => $limitTypes,
								'roles' => ['superadmin', 'administrator']
							],
							'value' => [
								'type' => 'number',
								'rules' => 'numeric|required',
								'roles' => ['superadmin', 'administrator']
							]
						],
						'rules' => 'array|nullable',
						'value' => $this->getModel()->getSpecialParameter('limits', [])
					],
				]
			]
		];
	}

	public function getSpecialParametersFieldsetParameters() : array
	{
		return [
			'parameters' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'decimals' => [
						'type' => 'number',
						'rules' => 'integer|min:1|max:4',
						'value' => $this->getDecimals()
					]
				]
			]
		];
	}
}