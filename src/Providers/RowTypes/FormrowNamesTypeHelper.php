<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
		$qualifiedClass = static::getFormrowsNamespace() . ucfirst($type);

		return $qualifiedClass::getTranslatedName();
	}

	static function getByType(string $type) : BaseRow
	{
		$className = static::getFormrowsNamespace() . $type;

		return new $className();
	}

	static function getPossibleTypesArray() : array
	{
		$filesInFolder = File::files(static::getBasePath());

		$result = [];

		foreach($filesInFolder as $file)
		{
			$qualifiedClass = static::getClassFromFilePath($file);

			$type = $qualifiedClass::getType();
			$name = $qualifiedClass::getTranslatedName();

			$result[$type] = $name;
		}

		return $result;
	}
}