<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowWithSpecialParametersInterface;
use IlBronza\FileCabinet\Providers\RowTypes\SpecialParametersTrait;
use IlBronza\FileCabinet\Providers\RowTypes\StandardCheckFieldValidityParametersTrait;
use IlBronza\FormField\Fields\JsonFormField;
use IlBronza\FormField\FormField;
use Illuminate\Support\Facades\Validator;

use function array_filter;
use function array_keys;
use function implode;
use function is_array;
use function is_string;
use function json_decode;
use function json_encode;

class FormrowJson extends BaseRow implements FormrowWithSpecialParametersInterface
{
	use StandardCheckFieldValidityParametersTrait;
	use SpecialParametersTrait;

	static $fieldType = 'json';
	static $databaseField = 'text';

	public function getDefaultRules() : array
	{
		// JsonFormField posts an array of rows (even for a single row)
		return [
			'array'
		];
	}

	public function transformValue(mixed $databaseValue) : mixed
	{
		if (is_array($databaseValue))
			return $databaseValue;

		if (is_string($databaseValue) && $databaseValue !== '')
			return json_decode($databaseValue, true);

		return $databaseValue;
	}

	public function parseField(mixed $value) : mixed
	{
		// Store as JSON string in the `text` column
		if ($value === null)
			return null;

		if (is_string($value))
			return $value;

		return json_encode($value);
	}

	protected function getAvailableSchemaFieldTypes() : array
	{
		return [
			'text' => 'Testo',
			'number' => 'Numero',
			'date' => 'Data',
			'boolean' => 'Booleano',
			'textarea' => 'Testo lungo',
			'select' => 'Lista',
		];
	}

	public function getSpecialParametersFieldsetParameters() : array
	{
		return [
			'parameters' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'schema' => [
						'type' => 'json',
						'fields' => [
							'key' => ['text' => 'string|required|max:64'],
							'label' => ['text' => 'string|nullable|max:255'],
							'type' => [
								'type' => 'select',
								'multiple' => false,
								'select2' => false,
								'list' => $this->getAvailableSchemaFieldTypes(),
								'rules' => 'string|required|in:' . implode(',', array_keys($this->getAvailableSchemaFieldTypes()))
							],
							'possibleValues' => [
								'type' => 'textarea',
								'tooltip' => 'Uno per riga oppure separati da virgola',
								'rules' => 'string|nullable|max:2048',
								'vertical' => true
							],
							'required' => [
								'type' => 'select',
								'multiple' => false,
								'select2' => false,
								'list' => [
									'0' => 'No',
									'1' => 'Sì'
								],
								'rules' => 'string|nullable|in:0,1'
							],
						],
						'rules' => 'array|required',
						'value' => $this->getModel()->getSpecialParameter('schema', [])
					]
				]
			]
		];
	}

	public function getChildrenFields() : array
	{
		// `JsonFormField` expects an associative array of inner field parameters
		// keyed by field name. The schema is stored inside the formrow special
		// parameters (`parameters.schema`) as rows with key/label/type/required.
		if (! is_array($this->getSchema()))
			return [];

		return $this->buildInnerFieldsParametersFromSchema();
	}

	protected function getSchema() : array
	{
		return $this->getModel()->getSpecialParameter('schema', []);
	}

	protected function buildInnerFieldsParametersFromSchema() : array
	{
		$result = [];

		foreach ($this->getSchema() as $schemaRow)
		{
			$key = $schemaRow['key'] ?? null;
			if (! $key)
				continue;

			$type = $schemaRow['type'] ?? 'text';
			$required = ! empty($schemaRow['required']);

			$field = $this->buildInnerFieldParametersFromSchemaRow($schemaRow, $type, $required);

			$result[$key] = $field;
		}

		return $result;
	}

	protected function buildInnerFieldParametersFromSchemaRow(array $schemaRow, string $type, bool $required) : array
	{
		$label = $schemaRow['label'] ?? null;

		if ($type === 'select')
		{
			$list = $this->buildSelectListFromSchemaRow($schemaRow);

			$rules = array_filter([
				$required ? 'required' : 'nullable',
				count($list) ? ('in:' . implode(',', array_keys($list))) : null
			]);

			return [
				'type' => 'select',
				'multiple' => false,
				'select2' => false,
				'list' => $list,
				'rules' => implode('|', $rules),
				'label' => $label
			];
		}

		$rules = array_filter([
			$type === 'number' ? 'numeric' : ($type === 'boolean' ? 'boolean' : 'string'),
			$required ? 'required' : 'nullable',
			'max:255'
		]);

		return [
			'type' => $type,
			'rules' => implode('|', $rules),
			'label' => $label,
		];
	}

	protected function buildSelectListFromSchemaRow(array $schemaRow) : array
	{
		$possibleValues = $schemaRow['possibleValues'] ?? null;

		// Legacy format: [{value: "..."}]
		if (is_array($possibleValues))
		{
			$result = [];
			foreach ($possibleValues as $element)
			{
				$value = $element['value'] ?? null;
				if (! is_string($value) || $value === '')
					continue;

				$result[$value] = $value;
			}

			return $result;
		}

		// New format: multiline or CSV string
		if (! is_string($possibleValues))
			return [];

		$raw = trim($possibleValues);
		if ($raw === '')
			return [];

		$parts = preg_split("/[\\n,]+/", $raw);
		$result = [];
		foreach ($parts as $part)
		{
			$value = trim($part);
			if ($value === '')
				continue;

			$result[$value] = $value;
		}

		return $result;
	}

	public function getFormField() : FormField
	{
		return new JsonFormField([
			'fields' => $this->buildInnerFieldsParametersFromSchema(),
			'vertical' => true
		]);
	}

	public function getValidationRulesArrayFromSpecialParametersArray() : array
	{
		// Extra validation is performed in storeDossierrow()
		return [];
	}

	protected function getValidationRulesForStoredValue(array $value) : array
	{
		$rules = [];

		$innerFields = $this->buildInnerFieldsParametersFromSchema();

		foreach ($innerFields as $key => $field)
		{
			$fieldRules = $field['rules'] ?? null;
			if (! $fieldRules)
				continue;

			// JsonFormField produces an array of rows: value[0][marca], value[1][marca], ...
			$rules["value.*.$key"] = $fieldRules;
		}

		return $rules;
	}

	public function storeDossierrow(Dossierrow $dossierrow, mixed $value, bool $validate = false) : bool
	{
		if ($validate)
		{
			$validator = Validator::make(
				['value' => $value],
				['value' => $this->getDefaultRules()]
			);

			if ($validator->fails())
				throw new \Exception(json_encode($validator->getMessageBag()->getMessages()));

			if (is_array($value))
			{
				$innerValidator = Validator::make(
					['value' => $value],
					$this->getValidationRulesForStoredValue($value)
				);

				if ($innerValidator->fails())
					throw new \Exception(json_encode($innerValidator->getMessageBag()->getMessages()));
			}
		}

		return $this->_storeDossierrow(
			$dossierrow,
			$this->parseField($value),
			$validate
		);
	}

	public function renderValueForView($value) : ?string
	{
		$value = $this->transformValue($value);

		if (! is_array($value))
			return parent::renderValueForView($value);

		$schema = $this->getSchema();
		$labelByKey = [];
		foreach ($schema as $schemaRow)
			if (! empty($schemaRow['key']))
				$labelByKey[$schemaRow['key']] = $schemaRow['label'] ?? $schemaRow['key'];

		$rows = [];
		foreach ($value as $row)
		{
			if (! is_array($row))
				continue;

			$parts = [];
			foreach ($row as $k => $v)
			{
				if ($v === null || $v === '')
					continue;

				$label = $labelByKey[$k] ?? $k;
				$parts[] = $label . ': ' . $v;
			}

			if (count($parts))
				$rows[] = implode(' / ', $parts);
		}

		return implode('<br />', $rows);
	}
}

