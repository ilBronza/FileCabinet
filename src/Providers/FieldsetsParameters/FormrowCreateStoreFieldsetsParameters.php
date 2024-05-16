<?php

namespace IlBronza\FileCabinet\Providers\FieldsetsParameters;

use IlBronza\FileCabinet\Providers\RowTypes\FormrowNamesTypeHelper;
use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class FormrowCreateStoreFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters() : array
    {
        $rowTypes = FormrowNamesTypeHelper::getPossibleTypesArray();

        return [
            'package' => [
                'translationPrefix' => 'filecabinet::fields',
                'fields' => [
                    'name' => ['text' => 'string|required|max:255'],
                    'slug' => ['text' => 'string|nullable|max:255'],
                    'required' => ['boolean' => 'bool|required'],
                    'repeatable' => ['boolean' => 'bool|required'],
                    'description' => ['texteditor' => 'string|nullable|max:2048'],
                    'type' => [
                        'type' => 'select',
                        'multiple' => false,
                        'select2' => false,
                        'list' => $rowTypes,
                        'rules' => 'string|required|in:' . implode(",", array_keys($rowTypes))
                    ]
                ]
            ]
        ];
    }
}
