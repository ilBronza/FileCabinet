<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FormField\Fields\BooleanFormField;
use IlBronza\FormField\FormField;

class FormrowBoolean extends BaseRow
{
	static $fieldType = 'boolean';
	static $databaseField = 'boolean';

	public function getDefaultRules() : array
	{
		return [
			'boolean'
		];
	}

	public function getFormField() : FormField
	{
		return new BooleanFormField();
	}
}