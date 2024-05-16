<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FormField\Fields\DatetimeFormField;
use IlBronza\FormField\FormField;

class FormrowDatetime extends FormrowDate
{
	static $fieldType = 'datetime';

	public function transformValue(mixed $databaseValue) : mixed
	{
		return $databaseValue;
	}

	public function getFormField() : FormField
	{
		return new DatetimeFormField();
	}
}