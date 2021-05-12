<?php

namespace IlBronza\FileCabinet\Http\Controllers\CRUDTraits;

trait CRUDFilecabinetrowParametersTrait
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
                'type' => 'flat',
                'parameters' => 'flat',
                'description' => 'flat',
                'icon' => 'flat',
                'compulsory' => 'boolean',
                'mySelfDelete' => 'links.delete'
            ]
        ],

        'related' => [
            'fields' => 
            [
                'mySelfPrimary' => 'primary',
                'mySelfEdit' => 'links.edit',
                'mySelfSee' => 'links.see',
                'name' => 'flat',
                'slug' => 'flat',
                'type' => 'flat',
                'mySelfDelete' => 'links.delete'
            ]
        ]
    ];

    static $formFields = [
        'common' => [
            'default' => [
                'name' => ['text' => 'string|required|max:191'],
                'slug' => ['text' => 'string|nullable|max:191'],
                'type' => ['select' => 'string|required'],
                'parameters' => ['textarea' => 'string|nullable|max:1024000'],
                'description' => ['texteditor' => 'string|nullable|max:10240'],
                'icon' => ['text' => 'string|required|max:191'],
                'compulsory' => ['boolean' => 'string|required'],
                'nullable' => ['boolean' => 'boolean|required'],
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