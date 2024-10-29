<?php

namespace IlBronza\FileCabinet\Providers\FieldsetsParameters;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

use function array_merge;

class FormrowEditFieldsetsParameters extends FormrowCreateStoreFieldsetsParameters
{
    public function _getFieldsetsParameters() : array
    {
        $defaultParameters = parent::_getFieldsetsParameters();

        if(! $this->getModel()->hasSpecialParameters())
            return $defaultParameters;

	    $specialParameters = $this->getModel()->getRowType()->getSpecialParametersFieldsetParameters();
	    $validationParameters = $this->getModel()->getRowType()->getCheckFieldValidityParametersFieldsetParameters();

        $result = array_merge(
            $defaultParameters,
	        $specialParameters,
	        $validationParameters,
        );

        return $result;
    }
}
