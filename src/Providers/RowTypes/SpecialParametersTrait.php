<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use IlBronza\Form\Helpers\FieldsetsExtractorHelper;

trait SpecialParametersTrait
{
	public function getSpecialParametersFields() : array
	{
		return FieldsetsExtractorHelper::getFieldsParametersByFieldsetsParametersArray(
			$this->getSpecialParametersFieldsetParameters()
		);
	}
}