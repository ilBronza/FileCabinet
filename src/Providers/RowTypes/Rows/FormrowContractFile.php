<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FormField\FormField;

class FormrowContractFile extends FormrowFile
{
	static $fieldType = 'filecabinet::providers.formFields.contractFile';

	static public function getType() : string
	{
		return 'contract-file';
	}

	public function getSpecialParametersFieldsetParameters() : array
	{
		$parameters = parent::getSpecialParametersFieldsetParameters();

		$parameters['parameters']['fields']['urlContractGetterMethod'] = [
			'type' => 'text',
			'rules' => 'nullable|string|max:255',
			'value' => $this->getModel()->getSpecialParameter('urlContractGetterMethod', null)
		];

		return $parameters;
	}

	public function getFormField() : FormField
	{
		return new \IlBronza\FileCabinet\Providers\FormFields\ContractFileFormField();
	}
}
