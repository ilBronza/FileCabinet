<?php

namespace IlBronza\FileCabinet\Providers\FieldsetsParameters;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class FilecabinetTemplatePdfTemplateFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters() : array
    {
        $categoryTree = $this->getModel()->getCategory()->getTree();

        // dd(
        //     get_class_methods(
        //         $categoryTree
        //     )
        // );

        return [
            'output' => [
                'translationPrefix' => 'filecabinet::fields',
                'fields' => [
                    'pdf_template' => [
                        'type' => 'textarea',
                        'rules' => 'string|nullable',
                        'showLabel' => false
                    ],
                ],
                'width' => ['expand']
            ],
            'availableFields' => [
                'fields' => [],
                'view' => [
                    'name' => 'filecabinet::filecabinetTemplates.pdfTemplate',
                    'parameters' => [
                        'categoryTree' => $categoryTree
                    ]
                ],
                'width' => ['xlarge']
            ],
        ];
    }
}
