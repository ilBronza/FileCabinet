<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Helpers\FormFieldsProvider\FormFieldsProvider;
use IlBronza\FormField\Helpers\FormFieldsProvider\FormfieldParametersHelper;

use function stripos;

class DossierrowFormFieldHelper
{
    static function createFieldFromDossierrow(Dossierrow $dossierrow) : FormField
    {
        $fieldname = $dossierrow->getKey();
        $parameters = FormfieldParametersHelper::extractFromModel($dossierrow);

        $parameters['model'] = $dossierrow;

        return FormFieldsProvider::createByNameParameters(
                $fieldname,
                $parameters
            );
    }


	static function getPossibleSelectValues(Dossierrow $dossierrow) : array
	{

	}
}