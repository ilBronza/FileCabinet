<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\CRUD\Helpers\ModelHelpers\ModelSchemaHelper;
use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowWithSpecialParametersInterface;
use IlBronza\FileCabinet\Providers\RowTypes\SpecialParametersTrait;
use IlBronza\FormField\Fields\TextFormField;
use IlBronza\FormField\FormField;

class FormrowModelAttribute extends BaseRow implements FormrowWithSpecialParametersInterface
{
	use SpecialParametersTrait;
	// static $databaseField = 'string';

	public function getFormfieldType() : string
	{
		dd($this);
		dd('qua va selezionato il tipo di campo corretto');
		return static::$fieldType;
	}

	public function getDefaultRules() : array
	{
		dd('altrettanti casini');
		return [
			$this->isRepeatable() ? 'array' : 'string',
			'max:' . $this->max
		];
	}

	public function getFormField() : FormField
	{
		dd('cossa xe');
		return new TextFormField();
	}

	public function getPossibleAttributesArray() : array
	{
		$result = [];

		foreach($this->getModel()->getForm()->getPossibleModels() as $name => $class)
		{
			$fields = ModelSchemaHelper::getModelDatabaseFieldsByClass(
            	$class
        	);

        	foreach($fields as $field)
        		$result["{$name}.{$field}"] = "{$name}.{$field}";
		}

		return $result;
	}

	public function getSpecialParametersFieldsetParameters() : array
	{
		return [
			'parameters' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'attribute_name' => [
						'type' => 'select',
						'multiple' => true,
						'rules' => 'array|nullable|in:' . implode(',', array_keys($this->getPossibleAttributesArray())),
						'list' => $this->getPossibleAttributesArray()
					]
				]
			]
		];
	}

}