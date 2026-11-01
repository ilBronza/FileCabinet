<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

trait MultipleModelReferencesTrait
{
	public function transformValue(mixed $databaseValue) : mixed
	{
		if ($databaseValue === null || is_array($databaseValue))
			return $databaseValue;

		return json_decode($databaseValue, true, 512, JSON_THROW_ON_ERROR);
	}

	public function parseField(mixed $value) : mixed
	{
		return $value === null ? null : json_encode(array_values($value), JSON_THROW_ON_ERROR);
	}
}
