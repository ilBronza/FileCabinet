<?php

namespace IlBronza\FileCabinet\Providers\FieldsetsParameters;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class FormEditUpdateFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters() : array
    {
        return [
            'package' => [
                'translationPrefix' => 'filecabinet::fields',
                'fields' => [
                    'name' => ['text' => 'string|required|max:255'],
                    'slug' => ['text' => 'string|nullable|max:255'],
                    'category' => [
                        'type' => 'select',
                        'label' => __('filecabinet::fields.mainCategory'),
                        'multiple' => false,
                        'mustBeSorted' => false,
                        'rules' => 'nullable|exists:' . config('category.models.category.table') . ',id',
                        'relation' => 'category'
                    ],
                    'categories' => [
                        'type' => 'select',
                        'multiple' => true,
                        'mustBeSorted' => false,
                        'rules' => 'nullable|exists:' . config('category.models.category.table') . ',id',
                        'relation' => 'categories'
                    ],
                    'repeatable' => [
                        'type' => 'boolean',
                        'tooltip' => __('filecabinet::fields.repeatableTooltip'),
                        'rules' => 'boolean|required'
                    ],
                    'description' => ['texteditor' => 'string|nullable|max:2048'],
                ],
                'width' => ['1-3@m']
            ],
            'interventions' => [
                'translationPrefix' => 'filecabinet::fields',
                'fields' => [
                    'prettyInterventions' => [
                        'type' => 'textarea',
                        'displayMode' => 'show',
                        'showLabel' => false,
                        'rules' => []
                    ]
                ],
                'width' => ['1-3@m']
            ],
            'stats' => [
                'translationPrefix' => 'filecabinet::fields',
                'fields' => [
                    'dossiersCount' => [
                        'type' => 'number',
                        'displayMode' => 'show',
                        'rules' => []
                    ]
                ],
                'width' => ['1-3@m']
            ]
        ];
    }
}
