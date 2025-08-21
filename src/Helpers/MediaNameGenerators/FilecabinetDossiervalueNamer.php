<?php

namespace IlBronza\FileCabinet\Helpers\MediaNameGenerators;

use IlBronza\FileCabinet\Helpers\MediaNameGenerators\FilecabinetDefaultFileNamer;

use function dd;
use function preg_match_all;
use function str_replace;

class FilecabinetDossiervalueNamer extends FilecabinetDefaultFileNamer
{
	/**
	 * @return mixed
	 */
	protected function getFormrowSubstitutionString() : string
	{
		return $this->getFormrow()->getSpecialParameter('nameValueRule', null);
	}

	protected function getBracketsContent(string $regex, string $valueSubstitutionString) : array
	{
		preg_match_all($regex, $valueSubstitutionString, $result);

		return $result[1];
	}

	protected function getFormrowsSlugs(mixed $valueSubstitutionString) : array
	{
		return $this->getBracketsContent("/\{{(.*?)}}/", $valueSubstitutionString);
	}

	protected function getMethodsNames(mixed $valueSubstitutionString) : array
	{
		return $this->getBracketsContent("/\[\[(.*?)]]/", $valueSubstitutionString);
	}

	protected function substituteAttributes(string $valueSubstitutionString) : string
	{
		$formrowsSlugs = $this->getFormrowsSlugs($valueSubstitutionString);

		foreach ($formrowsSlugs as $formrowname)
		{
			$dossierrow = $this->getDossier()->getDossierrowByFormrowSlug($formrowname);

			$valueSubstitutionString = str_replace("{{" . $formrowname . "}}", $dossierrow->getValue(), $valueSubstitutionString);
		}

		return $valueSubstitutionString;
	}

	protected function substituteMethods(string $valueSubstitutionString) : string
	{
		$methodNames = $this->getMethodsNames($valueSubstitutionString);

		foreach ($methodNames as $methodName)
		{
			$targetModel = $this->getDossierrow()->getDossierable();

			//if class has method
			if(! method_exists($targetModel, $methodName))
				throw new \Exception('Method ' . $methodName . ' not found in ' . get_class($targetModel));

			if(! $value = $targetModel->{$methodName}())
				throw new \Exception('Method ' . $methodName . ' not returning any value from ' . get_class($targetModel));

			$valueSubstitutionString = str_replace("[[" . $methodName . "]]", $value, $valueSubstitutionString);
		}

		return $valueSubstitutionString;
	}

	protected function _getBaseFilename() : string
	{
		$valueSubstitutionString = $this->getFormrowSubstitutionString();

		// Perform the regex match
		$valueSubstitutionString = $this->substituteAttributes($valueSubstitutionString);
		return $this->substituteMethods($valueSubstitutionString);
	}
}
