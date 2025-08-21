<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use IlBronza\Form\Helpers\FieldsetsExtractorHelper;

use function is_array;
use function is_string;

trait FormrowMultipleValuesTrait
{
	public function transformValue(mixed $databaseValue) : mixed
	{
		if(is_array($databaseValue))
			return $databaseValue;

		return json_decode($databaseValue ?? '');
	}	
}