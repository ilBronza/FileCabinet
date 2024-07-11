<?php

namespace IlBronza\FileCabinet\Providers\FieldsetsParameters;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class FilecabinetTemplateCreateStoreFieldsetsParameters extends FieldsetParametersFile
{
    public function getModelsArray() : array
    {
        return config('app.filecabinet.filecabinetTemplate.applicableToModels');
    }

    public function getEventsArray() : array
    {
        return config('app.filecabinet.filecabinetTemplate.events', [
            'created' => 'created',
            'updated' => 'updated',
            'saved' => 'saved'
        ]);
    }

    public function _getFieldsetsParameters() : array
    {
        return [
            'package' => [
                'translationPrefix' => 'filecabinet::fields',
                'fields' => [
                    'category' => [
                        'type' => 'select',
                        'multiple' => false,
                        'mustBeSorted' => false,
                        'rules' => 'nullable|exists:' . config('category.models.category.table') . ',id',
                        'relation' => 'category'
                    ],


                    'models' => [
                        'type' => 'json',
                        'fields' => [
                            'model' => [
                                'type' => 'select',
                                'multiple' => false,
                                'select2' => false,
                                'rules' => 'string|nullable|in:' . implode(',', array_keys($this->getModelsArray())),
                                'possibleValuesArray' => $this->getModelsArray(),
                                'roles' => ['superadmin', 'administrator']
                            ],
                            'event' => [
                                'type' => 'select',
                                'multiple' => false,
                                'select2' => false,
                                'rules' => 'string|nullable|in:' . implode(',', array_keys($this->getEventsArray())),
                                'possibleValuesArray' => $this->getEventsArray(),
                                'roles' => ['superadmin', 'administrator']
                            ]
                        ],
                        'rules' => 'array|nullable',
                    ],

                    // 'name' => ['text' => 'string|required|max:255'],
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
                    // 'populated_at' => ['datetime' => 'date|nullable'],
                    // 'must_be_updated' => ['boolean' => 'bool'],
                ],
                'width' => ['1-3@m']
            ]
        ];
    }
}
