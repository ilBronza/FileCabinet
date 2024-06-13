<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowListInterface;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowWithSpecialParametersInterface;
use IlBronza\FileCabinet\Providers\RowTypes\SpecialParametersTrait;
use IlBronza\FormField\Fields\SelectFormField;
use IlBronza\FormField\FormField;

class FormrowSingleSelect extends BaseRow implements FormrowWithSpecialParametersInterface, FormrowListInterface
{
	use SpecialParametersTrait;

	static $fieldType = 'select';
	static $databaseField = 'string';

	public function getDefaultRules() : array
	{
		return [
			'string'
		];
	}

	public function getPossibleValuesArray() : array
	{
		$array = $this->getModel()->getSpecialParameter('possibleValues', []);

		$result = [];

		foreach($array as $element)
			$result[$element['value']] = $element['value'];

		return $result;
	}

	public function getPossibleValuesValidationArray() : array
	{
		$fields = $this->getPossibleValuesArray();

		return [
			'in:' . implode(",", array_keys($fields))
		];
	}

	public function getSpecialParametersFieldsetParameters() : array
	{
		return [
			'parameters' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'possibleValues' => [
						'type' => 'json',
						'fields' => [
							'value' => ['text' => 'string|required|max:255'],
						],
						'rules' => 'array|required',
						'value' => $this->getModel()->getSpecialParameter('possibleValues', [])
					]
				]
			]
		];
	}

	public function getFormField() : FormField
	{
		return new SelectFormField();
	}
}