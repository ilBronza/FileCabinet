<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FileCabinet\Providers\RowTypes\FormrowMultipleValuesTrait;
use IlBronza\FormField\Fields\SelectFormField;
use IlBronza\FormField\FormField;

class FormrowMultipleSelect extends FormrowSingleSelect
{
	use FormrowMultipleValuesTrait;

	static $databaseField = 'text';

	public function getDefaultRules() : array
	{
		return [
			'array'
		];
	}

	public function getFormField() : FormField
	{
		return new SelectFormField();
	}
}