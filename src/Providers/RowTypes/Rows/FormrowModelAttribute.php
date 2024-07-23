<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\CRUD\Helpers\ModelHelpers\ModelSchemaHelper;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowWithSpecialParametersInterface;
use IlBronza\FileCabinet\Providers\RowTypes\Rows\ModelDatabaseTypes\DatabaseTypeHelper;
use IlBronza\FileCabinet\Providers\RowTypes\SpecialParametersTrait;
use IlBronza\FormField\Fields\TextFormField;
use IlBronza\FormField\FormField;
use Illuminate\Database\Eloquent\Model;

class FormrowModelAttribute extends BaseRow implements FormrowWithSpecialParametersInterface
{
	public string $attributeNameVariable;
	protected DatabaseTypeHelper $columnHelper;

	use SpecialParametersTrait;
	// static $databaseField = 'string';

	public function getSchemaColumnHelper() : DatabaseTypeHelper
	{
		if(isset($this->columnHelper))
			return $this->columnHelper;

		return $this->_getSchemaColumnHelper();
	}

	public function getPossibleValuesValidationArray() : array
	{
		return [];
	}

	public function getFieldname()
	{
		$attributeName = $this->getAttributeNameVariable();

		$pieces = explode(".", $attributeName);

		return $pieces[1];
	}

	public function getModelname()
	{
		$attributeName = $this->getAttributeNameVariable();

		$pieces = explode(".", $attributeName);

		return $pieces[0];
	}

	protected function getPlaceholderModel() : Model
	{
		$modelName = $this->getModelname();

		$modelClass = $this->getModel()->getForm()->getFullQualifiedModelByName(
			$modelName
		);

		return $modelClass::make();
	}

	public function _getSchemaColumnHelper() : DatabaseTypeHelper
	{
		$schemaColumn = ModelSchemaHelper::getFieldColumnByModel(
			$this->getPlaceholderModel(),
			$this->getFieldname()
		);

		return $this->columnHelper = DatabaseTypeHelper::createByColumn($schemaColumn);
	}

	public function getAttributeNameVariable() : string
	{
		if(isset($this->attributeNameVariable))
			return $this->attributeNameVariable;

		$parameters = $this->getModel()->getSpecialParameters();

		return $this->attributeNameVariable = $parameters['attribute_name'][0];
	}

	public function getSpecialParametersRuleArray() : array
	{
		$parameters = $this->getModel()->getSpecialParameters();

		return $parameters['rules'];
	}

	public function getSpecialParametersRuleValues() : array
	{
		return array_column(
			$this->getSpecialParametersRuleArray(),
			'rule'
		);
	}

	public function getDatabaseField() : string
	{
		return $this->getFormfieldType();
	}

	public function getFormfieldType() : string
	{
		return $this->getSchemaColumnHelper()->getType();
	}

	public function getDefaultRules() : array
	{
		$databaseDefaultParameters = $this->getSchemaColumnHelper()->getDefaultRules();

		$userDefinedRules = $this->getSpecialParametersRuleValues();

		if(in_array('required', $userDefinedRules))
			if (($key = array_search('nullable', $databaseDefaultParameters)) !== false)
			    unset($databaseDefaultParameters[$key]);

		if(in_array('nullable', $userDefinedRules))
			if (($key = array_search('required', $databaseDefaultParameters)) !== false)
			    unset($databaseDefaultParameters[$key]);

		return $databaseDefaultParameters + $userDefinedRules;
	}

	public function getFormField() : FormField
	{
		dd(
			ModelSchemaHelper::getFieldTypeByModel(
				$this->getModel()
			)
		);

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


	public function storeDossierrow(Dossierrow $dossierrow, mixed $value, bool $validate = false) : bool
	{
		$model = $dossierrow->getDossierable();
		$fieldname = $this->getFieldname();

		$model->{$fieldname} = $value;
		$model->save();

		return true;
	}

	public function getDossierrowValue(Dossierrow $dossierrow)
	{
		$model = $dossierrow->getDossierable();
		$fieldname = $this->getFieldname();

		// dd($model);
		// $databaseField = $this->getDatabaseField();

		// dd($databaseField);

		// $value = $dossierrow->$databaseField;

		return $this->transformValue($model->{$fieldname});
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
						'value' => $this->getAttributeNameVariable(),
						'rules' => 'array|nullable|in:' . implode(',', array_keys($this->getPossibleAttributesArray())),
						'list' => $this->getPossibleAttributesArray()
					],
					'rules' => [
                        'type' => 'json',
                        'value' => $this->getSpecialParametersRuleArray(),
                        'fields' => [
                            'rule' => ['text' => 'string|required|max:1024'],
                        ],
                        'rules' => 'array|nullable',
					],
				]
			]
		];
	}

}