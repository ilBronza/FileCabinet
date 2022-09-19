<?php

namespace IlBronza\FileCabinet\Http\Controllers\CRUDTraits;

trait CRUDFilecabinetParametersTrait
{
    public static $tables = [

        'index' => [
            'fields' => 
            [
                'mySelfPrimary' => 'primary',
                'mySelfEdit' => 'links.edit',
                'mySelfSee' => 'links.see',
                'name' => 'flat',
                'slug' => 'flat',
                'filecabinetrows' => 'relations.hasMany',
                // 'categories' => 'relations.belongsToMany',
                'mySelfDelete' => 'links.delete',
            /**
                'mySelfEdit' => 'links.edit',
                'mySelfSee' => [
                    'type' => 'links.see',
                    'textParameter' => false
                    'order' => [
                        'priority' => 10,
                        'type' => 'desc'
                    ],
                ],
                'rag_soc' => 'flat',
                'destination' => 'flat',
                'somet_text' => 'editor.text',
                'second_color' => 'editor.color',
                'supplier' => 'relations.belongsTo',
                'color' => 'color',
                'manyToManyModels' => [
                    'type' => 'relations.beongsToMany',
                    'pivot' => 'PivotModelBaseName'
                ],
                'relatedModels' => 'relations.hasMany',
                'belongsToModel' => 'relations.belongsTo',
                'zone' => 'flat',
                'mySelfDelete' => 'links.delete'
            **/
            ]
        ]
    ];

    static $formFields = [
        'common' => [
            'default' => [
                'name' => ['text' => 'string|required|max:191'],
                'slug' => ['text' => 'string|nullable|max:191'],
                'categories' => [
                    'type' => 'select',
                    'multiple' => true,
                    'rules' => 'array|nullable|exists:categories,id',
                    'relation' => 'categories'
                ],
        /**
                'age' => ['number' => 'numeric|required'],
                'color' => ['color' => 'numeric|required'],
                'dated_at' => ['date' => 'date|nullable'],
                'time_at' => ['datetime' => 'date|nullable'],
                'permissions' => [
                    'type' => 'select',
                    'multiple' => true,
                    'rules' => 'array|nullable|exists:permissions,id',
                    'relation' => 'permissions'
                ],
                'city' => [
                    'type' => 'select',
                    'multiple' => false,
                    'rules' => 'integer|nullable|exists:cities,id',
                    'relation' => 'city'
                ],
            ]
        **/
        ],
        /**
        'edit' => [
            'default' => [
            ]
        ],
        'onlyEdit' => [
            'default' => [
            ]
        ],
        'create' => [
            'default' => [
            ]
        ],
        'onlyCreate' => [
            'default' => [
            ]
        **/
        ],
    ];    
}