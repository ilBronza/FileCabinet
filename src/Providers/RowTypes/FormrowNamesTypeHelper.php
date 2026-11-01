<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;

class FormrowNamesTypeHelper
{
	/** 
	 * Returns the namespace of the classes in the folder
	 * 
	 * @return string
	 */
	static public function getNamespace() : string
	{
		return "IlBronza\FileCabinet\Providers\RowTypes\Rows\\";
	}

	/** 
	 * Returns the namespace of the formrows classes in the formrows folder
	 * 
	 * @return string
	 */
	static public function getFormrowsNamespace() : string
	{
		return static::getNamespace() . "Formrow";
	}

	/** 
	 * Returns the class name from the file path
	 * 
	 * @param string $filepath
	 * @return string
	 */
	static function getClassFromFilePath(string $filepath) : string
	{
		$className = pathinfo($filepath)['filename'];

		return static::getNamespace() . $className;
	}

	/** 
	 * Returns the base path of the formrows classes
	 * 
	 * @return string
	 */
	static function getBasePath() : string
	{
		return dirname(__FILE__) . "/Rows/";
	}

	static function getTranslatedNameByType(string $type) : string
	{
		return app(RowTypeRegistry::class)->info($type)['name'];
	}

	static function getByType(string $type) : BaseRow
	{
		return app(RowTypeRegistry::class)->make($type);
	}

	static function getPossibleTypesArray() : array
	{
		return app(RowTypeRegistry::class)->choices();
	}
}
