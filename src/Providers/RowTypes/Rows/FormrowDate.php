<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FileCabinet\Providers\RowTypes\StandardCheckFieldValidityParametersTrait;
use IlBronza\FormField\Fields\DateFormField;
use IlBronza\FormField\FormField;

class FormrowDate extends BaseRow
{
	use StandardCheckFieldValidityParametersTrait;
	static $fieldType = 'date';
	static $databaseField = 'timestamp';
	static public $datatableFieldString = 'dates.fromString.date';

	public function getDefaultRules() : array
	{
		return [
			'date'
		];
	}

	public function transformValue(mixed $databaseValue) : mixed
	{
		return substr($databaseValue, 0, 10);
	}

	public function getFormField() : FormField
	{
		return new DateFormField();
	}
}