<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows\ModelDatabaseTypes;

class DatabaseTypeTextHelper extends DatabaseTypeHelper
{
	static string $type = 'text';

	public function _getDefaultRules() : array
	{
		return [
			$this->getMaxStringRule(),
			$this->getRequiredStringRule(),
		];
	}
}