<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Helpers\FormFieldsProvider\FormFieldsProvider;
use IlBronza\FormField\Helpers\FormFieldsProvider\FormfieldParametersHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use function is_string;
use function method_exists;
use function trim;
use function stripos;

class DossierrowFormFieldHelper
{
    static function createFieldFromDossierrow(Dossierrow $dossierrow) : FormField
    {
        $fieldname = $dossierrow->getKey();
        $parameters = FormfieldParametersHelper::extractFromModel($dossierrow);
		$parameters = static::addContractFileParameters($dossierrow, $parameters);

        $parameters['model'] = $dossierrow;

        try
        {

            $formField = FormFieldsProvider::createByNameParameters(
                    $fieldname,
                    $parameters
                );

            $formField->addRowHtmlClass(
                Str::slug(
                    $dossierrow->getFormrow()->getName()
                )
            );
        }
        catch(\Exception $e)
        {
            dd($dossierrow->getFormfieldType());

            dd($dossierrow->formrow);
        }

        return $formField;
    }

	protected static function addContractFileParameters(Dossierrow $dossierrow, array $parameters) : array
	{
		if ($dossierrow->getFormrow()?->getType() != 'contract-file')
			return $parameters;

		$parameters['contractUrl'] = static::getContractUrl($dossierrow);

		return $parameters;
	}

	protected static function getContractUrl(Dossierrow $dossierrow) : ?string
	{
		$method = static::getContractUrlGetterMethod($dossierrow);

		if (! $method)
			return null;

		$dossierable = $dossierrow->getDossierable();

		if (! $dossierable instanceof Model)
			return null;

		if (! method_exists($dossierable, $method))
			return null;

		$url = $dossierable->{$method}();

		if (! is_string($url))
			return null;

		$url = trim($url);

		return $url !== '' ? $url : null;
	}

	protected static function getContractUrlGetterMethod(Dossierrow $dossierrow) : ?string
	{
		$method = $dossierrow->getFormrow()?->getSpecialParameter('urlContractGetterMethod');

		if (! is_string($method))
			return null;

		$method = trim($method);

		return $method !== '' ? $method : null;
	}


	static function getPossibleSelectValues(Dossierrow $dossierrow) : array
	{

	}
}
