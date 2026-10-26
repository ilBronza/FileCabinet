<?php

namespace IlBronza\FileCabinet\Providers\FieldsetsParameters;

use IlBronza\FileCabinet\Providers\FieldsetsParameters\Traits\PdfSettingsVisibilityTrait;

use function array_merge;

class FormrowEditFieldsetsParameters extends FormrowCreateStoreFieldsetsParameters
{
    use PdfSettingsVisibilityTrait;

    public function _getFieldsetsParameters() : array
    {
        $defaultParameters = parent::_getFieldsetsParameters();

        $defaultParameters['pdfSettings']['fields'] = [
            'pdf_show' => [
                'type' => 'boolean',
                'rules' => 'bool|nullable',
                'tooltip' => 'filecabinet::pdfOrdering.visibilityHelp',
            ],
            'pdf_sorting_index' => ['number' => 'integer|nullable|min:0|max:65535'],
        ] + $defaultParameters['pdfSettings']['fields'];

        $defaultParameters = $this->configurePdfSettingsVisibility($defaultParameters);

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
