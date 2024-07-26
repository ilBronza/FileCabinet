<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use IlBronza\Form\Helpers\FieldsetsExtractorHelper;

trait SpecialParametersTrait
{
	public function addSpecialParametersValidationRules()
	{
		foreach($this->getPossibleValuesValidationArray() as $rule)
			$this->setRule($rule);
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