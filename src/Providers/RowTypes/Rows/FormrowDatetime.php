<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FileCabinet\Providers\RowTypes\StandardCheckFieldValidityParametersTrait;
use IlBronza\FormField\Fields\DatetimeFormField;
use IlBronza\FormField\FormField;

class FormrowDatetime extends FormrowDate
{
	use StandardCheckFieldValidityParametersTrait;

	static $fieldType = 'datetime';
	static public $datatableFieldString = 'dates.datetime';

	public function transformValue(mixed $databaseValue) : mixed
	{
		return $databaseValue;
	}

	public function getFormField() : FormField
	{
		return new DatetimeFormField();
	}
}