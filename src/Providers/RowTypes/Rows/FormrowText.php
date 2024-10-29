<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FileCabinet\Providers\RowTypes\StandardCheckFieldValidityParametersTrait;
use IlBronza\FormField\Fields\TextFormField;
use IlBronza\FormField\FormField;

class FormrowText extends BaseRow
{
	use StandardCheckFieldValidityParametersTrait;

	static $fieldType = 'text';
	static $databaseField = 'string';

	public function __construct()
	{
		$this->max = config('filecabinet.defaultRules.text.max');
	}

	public function getDefaultRules() : array
	{
		return [
			$this->isRepeatable() ? 'array' : 'string',
			'max:' . $this->max
		];
	}

	public function getFormField() : FormField
	{
		return new TextFormField();
	}
}