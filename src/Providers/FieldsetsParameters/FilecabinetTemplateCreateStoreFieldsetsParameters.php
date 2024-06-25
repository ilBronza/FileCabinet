<?php

namespace IlBronza\FileCabinet\Providers\FieldsetsParameters;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class FilecabinetTemplateCreateStoreFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters() : array
    {
        return [
            'package' => [
                'translationPrefix' => 'filecabinet::fields',
                'fields' => [
                    'name' => ['text' => 'string|required|max:255'],
                    // 'form' => [
                    //     'type' => 'select',
                    //     'multiple' => false,
                    //     'rules' => 'nullable|exists:' . config('filecabinet.models.form.table') . ',id',
                    //     'relation' => 'form'
                    // ],
                    // 'dossierable' => [
                    //     'type' => 'select',
                    //     'multiple' => false,
                    //     'rules' => 'nullable',
                    //     'relation' => 'dossierable'
                    // ],
                    // 'populator' => [
                    //     'type' => 'select',
                    //     'multiple' => false,
                    //     'rules' => 'nullable',
                    //     'relation' => 'populator'
                    // ],
                    'populated_at' => ['datetime' => 'date|nullable'],
                    'must_be_updated' => ['boolean' => 'bool'],
                ],
                'width' => ['1-3@m']
            ]
        ];
    }
}
