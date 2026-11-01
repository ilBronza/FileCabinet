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

		if (! $this->getModel()->getRowType()->isAvailable())
		{
			$type = $this->getModel()->getType();
			$defaultParameters['baseData']['fields']['type'] = [
				'type' => 'select',
				'list' => [$type => $this->getModel()->getRowType()->message],
				'disabled' => true,
				'readOnly' => true,
				'rules' => 'nullable|in:' . $type,
			];
		}

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
