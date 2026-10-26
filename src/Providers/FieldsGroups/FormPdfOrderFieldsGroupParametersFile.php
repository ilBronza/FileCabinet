<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class FormPdfOrderFieldsGroupParametersFile extends FieldsGroupParametersFile
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
                'mySelfPdfFormrowsOrder' => [
                    'type' => 'links.link',
                    'faIcon' => 'file-pdf',
                    'function' => 'getPdfFormrowsOrderUrl',
                    'sortable' => false,
                ],
            ],
        ];
    }
}
