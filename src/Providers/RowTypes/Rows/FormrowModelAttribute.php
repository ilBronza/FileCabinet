<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\CRUD\Helpers\ModelHelpers\ModelSchemaHelper;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowWithSpecialParametersInterface;
use IlBronza\FileCabinet\Providers\RowTypes\Rows\ModelDatabaseTypes\DatabaseTypeHelper;
use IlBronza\FileCabinet\Providers\RowTypes\SpecialParametersRulesFieldTrait;
use IlBronza\FileCabinet\Providers\RowTypes\SpecialParametersTrait;
use IlBronza\FileCabinet\Providers\RowTypes\StandardCheckFieldValidityParametersTrait;
use IlBronza\FormField\Fields\TextFormField;
use IlBronza\FormField\FormField;
use Illuminate\Database\Eloquent\Model;

class FormrowModelAttribute extends BaseRow implements FormrowWithSpecialParametersInterface
{
	use StandardCheckFieldValidityParametersTrait;

	public string $attributeNameVariable;
	protected DatabaseTypeHelper $columnHelper;

	use SpecialParametersTrait;
	use SpecialParametersRulesFieldTrait;

	public function getSchemaColumnHelper() : DatabaseTypeHelper
	{
		if(isset($this->columnHelper))
			return $this->columnHelper;

		return $this->_getSchemaColumnHelper();
	}

	public function getValidationRulesArrayFromSpecialParametersArray() : array
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

		if($modelName == "")
			dd($this->getModel());

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

	public function getAttributeNameVariable() : ? string
	{
		if(isset($this->attributeNameVariable))
			return $this->attributeNameVariable;

		$parameters = $this->getModel()->getSpecialParameters();

		if(! isset($parameters['attribute_name']))
			return null;

		return $parameters['attribute_name'][0] ?? null;
	}

	public function getDatabaseField() : string
	{
		return $this->getFormfieldType();
	}

	public function getDefaultRules() : array
	{
		$databaseDefaultParameters = $this->getSchemaColumnHelper()->getDefaultRules();

		return $this->mergeSpecialParametersUserDefinedRules($databaseDefaultParameters);
	}

	public function getFormfieldType() : string
	{
		return $this->getSchemaColumnHelper()->getType();
	}

	public function getFormField() : FormField
	{
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

		if($model)
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
					'rules' => $this->getRulesFieldSpecialParametersArray()
				]
			]
		];
	}

}