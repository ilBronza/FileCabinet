<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class FormrowPdfOrderFieldsGroupParametersFile extends FieldsGroupParametersFile
{
    public static function getFieldsGroup(): array
    {
        return [
            'translationPrefix' => 'filecabinet::fields',
            'fields' => [
                'mySelfPrimary' => 'primary',
                'pdf_sorting_position' => 'utilities.sorting',
                'name' => ['type' => 'flat', 'sortable' => false],
                'sorting_index' => ['type' => 'flat', 'sortable' => false],
            ],
        ];
    }
}
