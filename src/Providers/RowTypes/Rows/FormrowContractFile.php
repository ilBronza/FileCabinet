<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

class FormrowContractFile extends FormrowFile
{
	static $fieldType = 'contractFile';

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

}
