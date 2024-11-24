<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use IlBronza\FileCabinet\Providers\RowTypes\Rows\FormrowSingleSelect;
use IlBronza\FormField\Fields\SelectFormField;
use IlBronza\FormField\FormField;

abstract class BaseModelRelationRow extends FormrowSingleSelect
{
	static $databaseField = 'text';

	public function getFormField() : FormField
	{
		return new SelectFormField();
	}
}