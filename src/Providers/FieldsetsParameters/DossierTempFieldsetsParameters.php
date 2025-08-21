<?php

namespace IlBronza\FileCabinet\Providers\FieldsetsParameters;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class DossierTempFieldsetsParameters extends FormrowCreateStoreFieldsetsParameters
{
    public array $fieldsetsParameters;

    public function _getFieldsetsParameters() : array
    {
        return $fieldsetsParameters;
    }
}
