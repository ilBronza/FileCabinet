<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows\ModelDatabaseTypes;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;

abstract class DatabaseTypeHelper
{
	public Column $column;

	abstract public function _getDefaultRules() : array;

	public function __construct(Column $column)
	{
		$this->setColumn($column);
	}

	public function setColumn(Column $column)
	{
		$this->column = $column;
	}

	public function getColumn() : Column
	{
		return $this->column;
	}

	public function getType() : string
	{
		return static::$type;
	}

	static function getHelperClassByFieldType(Type $fieldType)
	{
		$name = ucfirst(
			$fieldType->getName()
		);

		return __NAMESPACE__ . "\DatabaseType{$name}Helper";
	}

	static function createByColumn(Column $column)
	{
		$helperClass = static::getHelperClassByFieldType($column->getType());

		return new $helperClass($column);
	}







	public function getDefaultRules() : array
	{
		$rules = $this->_getDefaultRules();

		return array_filter($rules);
	}

	public function getMaxStringRule() : string
	{
		return 'max:' . $this->getColumn()->getLength();
	}

	public function getRequiredStringRule() : string
	{
		if($this->getColumn()->getNotnull())
			return 'required';

		return 'nullable';
	}
}