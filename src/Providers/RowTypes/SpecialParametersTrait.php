<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use IlBronza\Form\Helpers\FieldsetsExtractorHelper;

trait SpecialParametersTrait
{
	abstract public function getValidationRulesArrayFromSpecialParametersArray() : array;

	public function addSpecialParametersValidationRules()
	{
		foreach($this->getValidationRulesArrayFromSpecialParametersArray() as $rule)
			$this->setRule($rule);
	}

	public function getCheckFieldValidityParametersFields() : array
	{
		return FieldsetsExtractorHelper::getFieldsParametersByFieldsetsParametersArray(
			$this->getCheckFieldValidityParametersFieldsetParameters()
		);
	}

	public function getSpecialParametersFields() : array
	{
		return FieldsetsExtractorHelper::getFieldsParametersByFieldsetsParametersArray(
			$this->getSpecialParametersFieldsetParameters()
		);
	}

	public function getSpecialParametersSingleAttributeValue(string $attributeName) : ? string
	{
		if(isset($this->$attributeName))
			return $this->$attributeName;

		$parameters = $this->getModel()->getSpecialParameters();

		return $parameters[$attributeName] ?? null;
	}

}