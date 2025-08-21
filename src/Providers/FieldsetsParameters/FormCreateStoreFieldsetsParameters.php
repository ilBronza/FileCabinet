<?php

namespace IlBronza\FileCabinet\Providers\FieldsetsParameters;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class FormCreateStoreFieldsetsParameters extends FieldsetParametersFile
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
                        'label' => 'mainCategory',
                        'multiple' => false,
                        'mustBeSorted' => false,
                        'rules' => 'nullable|exists:' . config('category.models.category.table') . ',id',
                        'relation' => 'category'
                    ],
                    'parent_id' => [
                        'type' => 'select',
                        'label' => 'parentForm',
                        'multiple' => false,
                        'rules' => 'nullable|exists:' . config('filecabinet.models.form.table') . ',id',
                        'relation' => 'parent'
                    ],
                    'sorting_index' => ['number' => 'integer|nullable|min:0|max:65535'],
                    'repeatable' => [
                        'type' => 'boolean',
                        'tooltip' => 'filecabinet::fields.repeatableTooltip',
                        'rules' => 'boolean|required'
                    ],
                    'description' => ['texteditor' => 'string|nullable|max:2048'],
                ]
            ]
        ];
    }
}
