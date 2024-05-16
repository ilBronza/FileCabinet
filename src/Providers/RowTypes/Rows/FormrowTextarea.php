<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FormField\Fields\TextareaFormField;
use IlBronza\FormField\FormField;

class FormrowTextarea extends BaseRow
{
	static $fieldType = 'textarea';
	static $databaseField = 'text';

	public function __construct()
	{
		$this->max = config('filecabinet.defaultRules.textarea.max');
	}

	public function getDefaultRules() : array
	{
		return [
			'string',
			'max:' . $this->max
		];
	}

	public function getFormField() : FormField
	{
		return new TextareaFormField();
	}
}