<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class FormrowRelatedFieldsGroupParametersFile extends FormrowFieldsGroupParametersFile
{
    static function getFieldsGroup() : array
    {
        $result = parent::getFieldsGroup();

        unset($result['fields']['form']);

        return $result;
    }

}