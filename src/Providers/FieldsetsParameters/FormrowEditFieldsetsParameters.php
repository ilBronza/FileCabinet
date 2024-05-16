<?php

namespace IlBronza\FileCabinet\Providers\FieldsetsParameters;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class FormrowEditFieldsetsParameters extends FormrowCreateStoreFieldsetsParameters
{
    public function _getFieldsetsParameters() : array
    {
        $defaultParameters = parent::_getFieldsetsParameters();

        if(! $this->getModel()->hasSpecialParameters())
            return $defaultParameters;

        $specialParameters = $this->getModel()->getRowType()->getSpecialParametersFieldsetParameters();
        
        $result = array_merge(
            $defaultParameters,
            $specialParameters
        );

        return $result;
    }
}
