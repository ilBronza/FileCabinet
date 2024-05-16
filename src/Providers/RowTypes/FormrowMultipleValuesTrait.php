<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use IlBronza\Form\Helpers\FieldsetsExtractorHelper;

trait FormrowMultipleValuesTrait
{
	public function transformValue(mixed $databaseValue) : mixed
	{
		return json_decode($databaseValue ?? '');
	}	
}