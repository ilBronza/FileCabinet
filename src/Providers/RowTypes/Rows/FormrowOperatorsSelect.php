<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FileCabinet\Providers\RowTypes\FormrowMultipleValuesTrait;
use IlBronza\FileCabinet\Providers\RowTypes\StandardCheckFieldValidityParametersTrait;

class FormrowOperatorsSelect extends FormrowOperatorSelect
{
	use StandardCheckFieldValidityParametersTrait;

	use FormrowMultipleValuesTrait;

	public function getDefaultRules() : array
	{
		if($this->isRepeatable())
			dd('maneggiami questo');

		return [
			'array'
		];
	}
}