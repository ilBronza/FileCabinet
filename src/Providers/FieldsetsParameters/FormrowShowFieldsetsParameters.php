<?php

namespace IlBronza\FileCabinet\Providers\FieldsetsParameters;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class FormrowShowFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters() : array
    {
        return [
            'package' => [
                'fields' => [
                    'name' => ['text' => 'string|required|max:255'],
                    'slug' => ['text' => 'string|nullable|max:255'],
                ]
            ]
        ];
    }
}
