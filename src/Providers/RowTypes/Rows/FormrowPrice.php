<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FileCabinet\Providers\RowTypes\StandardCheckFieldValidityParametersTrait;
use IlBronza\FormField\Fields\NumberFormField;
use IlBronza\FormField\FormField;

class FormrowPrice extends BaseRow
{
	use StandardCheckFieldValidityParametersTrait;

	static $fieldType = 'number';
	static $databaseField = 'decimal';

	public int $min = -999999999999;
	public int $max = 999999999999;

	public function getDefaultRules() : array
	{
		return [
			'decimal',
			'min:' . $this->min,
			'max:' . $this->max
		];
	}

	public function getFormField() : FormField
	{
		return new NumberFormField();
	}
}