<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Helpers\FormFieldsProvider\FormFieldsProvider;
use IlBronza\FormField\Helpers\FormFieldsProvider\FormfieldParametersHelper;
use Illuminate\Support\Str;
use function stripos;

class DossierrowFormFieldHelper
{
    static function createFieldFromDossierrow(Dossierrow $dossierrow) : FormField
    {
        $fieldname = $dossierrow->getKey();
        $parameters = FormfieldParametersHelper::extractFromModel($dossierrow);

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


	static function getPossibleSelectValues(Dossierrow $dossierrow) : array
	{

	}
}