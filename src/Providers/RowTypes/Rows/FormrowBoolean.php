<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowWithSpecialParametersInterface;
use IlBronza\FileCabinet\Providers\RowTypes\SpecialParametersTrait;
use IlBronza\FileCabinet\Providers\RowTypes\StandardCheckFieldValidityParametersTrait;
use IlBronza\FormField\Fields\BooleanFormField;
use IlBronza\FormField\FormField;

class FormrowBoolean extends BaseRow implements FormrowWithSpecialParametersInterface
{
	static public $datatableFieldString = 'boolean';
	use StandardCheckFieldValidityParametersTrait;
	use SpecialParametersTrait;

	static $fieldType = 'boolean';
	static $databaseField = 'boolean';

	public function getDefaultRules() : array
	{
		return [
			'boolean'
		];
	}

	public function transformValue(mixed $databaseValue) : ? bool
	{
		return $databaseValue;
	}

	public function renderValueForView($value) : ?string
	{
		if ($value === null)
			return null;

		$parameterName = $value ? 'render_true' : 'render_false';

		return $this->getModel()->getSpecialParameter(
			$parameterName,
			config('filecabinet.formrows.boolean.' . $parameterName, $value ? 'Sì' : 'No')
		);
	}

	public function getValidationRulesArrayFromSpecialParametersArray() : array
	{
		return [];
	}

	public function getSpecialParametersFieldsetParameters() : array
	{
		return [
			'parameters' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'render_true' => [
						'type' => 'text',
						'rules' => 'string|nullable|max:255',
						'value' => $this->getModel()->getSpecialParameter('render_true'),
						'placeholder' => config('filecabinet.formrows.boolean.render_true', 'Sì'),
						'tooltip' => 'filecabinet::fields.boolean_render_override_help',
					],
					'render_false' => [
						'type' => 'text',
						'rules' => 'string|nullable|max:255',
						'value' => $this->getModel()->getSpecialParameter('render_false'),
						'placeholder' => config('filecabinet.formrows.boolean.render_false', 'No'),
						'tooltip' => 'filecabinet::fields.boolean_render_override_help',
					],
				],
			],
		];
	}

	public function getFormField() : FormField
	{
		return new BooleanFormField();
	}
}
