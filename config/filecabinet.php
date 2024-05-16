<?php

use IlBronza\FileCabinet\Http\Controllers\Dossierrows\DossierrowAddInstanceController;
use IlBronza\FileCabinet\Http\Controllers\Dossierrows\DossierrowShowController;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierCreateNewInstanceController;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierDestroyController;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierIndexController;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierUpdateController;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierUpdateFieldsController;
use IlBronza\FileCabinet\Http\Controllers\Filecabinets\FilecabinetPdfController;
use IlBronza\FileCabinet\Http\Controllers\Filecabinets\FilecabinetPopulateController;
use IlBronza\FileCabinet\Http\Controllers\Filecabinets\FilecabinetShowController;
use IlBronza\FileCabinet\Http\Controllers\FormAttaching\FormAttachByCategory;
use IlBronza\FileCabinet\Http\Controllers\Formrows\FormrowCreateStoreController;
use IlBronza\FileCabinet\Http\Controllers\Formrows\FormrowDestroyController;
use IlBronza\FileCabinet\Http\Controllers\Formrows\FormrowEditUpdateController;
use IlBronza\FileCabinet\Http\Controllers\Formrows\FormrowIndexController;
use IlBronza\FileCabinet\Http\Controllers\Formrows\FormrowShowController;
use IlBronza\FileCabinet\Http\Controllers\Forms\FormCreateStoreController;
use IlBronza\FileCabinet\Http\Controllers\Forms\FormDestroyController;
use IlBronza\FileCabinet\Http\Controllers\Forms\FormEditUpdateController;
use IlBronza\FileCabinet\Http\Controllers\Forms\FormIndexController;
use IlBronza\FileCabinet\Http\Controllers\Forms\FormShowController;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\DossierFilecabinet;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Filecabinet;
use IlBronza\FileCabinet\Models\Form;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\FileCabinet\Providers\FieldsGroups\DossierFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsGroups\DossierRelatedFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsGroups\FormFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsGroups\FormrowFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsGroups\FormrowRelatedFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\FormCreateStoreFieldsetsParameters;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\FormEditUpdateFieldsetsParameters;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\FormShowFieldsetsParameters;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\FormrowCreateStoreFieldsetsParameters;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\FormrowEditFieldsetsParameters;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\FormrowShowFieldsetsParameters;
use IlBronza\FileCabinet\Providers\RelationshipsManagers\FormRelationManager;

// use IlBronza\Category\Models\Category;
// use IlBronza\FileCabinet\Models\Filecabinet;

// return [
// 	'categories' => [
// 		'model' => Category::class,
// 		'collection' => 'filecabinet'
// 	],
// 	'filecabinet' => [
// 		'model' => Filecabinet::class
// 	] 
// ];


return [
    'routePrefix' => 'ibFilecabinet',

    'defaultRules' => [
        'textarea' => [
            'max' => 2048
        ],
        'text' => [
            'max' => 255
        ]
    ],

    'models' => [
        'form' => [
            'class' => Form::class,
            'table' => 'filecabinets__forms',
            'fieldsGroupsFiles' => [
                'index' => FormFieldsGroupParametersFile::class
            ],
            'parametersFiles' => [
                'create' => FormCreateStoreFieldsetsParameters::class,
                'edit' => FormEditUpdateFieldsetsParameters::class,
                'show' => FormShowFieldsetsParameters::class
            ],
            'relationshipsManagerClasses' => [
                'show' => FormRelationManager::class
            ],
            'controllers' => [
                'index' => FormIndexController::class,
                'create' => FormCreateStoreController::class,
                'store' => FormCreateStoreController::class,
                'show' => FormShowController::class,
                'edit' => FormEditUpdateController::class,
                'update' => FormEditUpdateController::class,
                'destroy' => FormDestroyController::class,
                'attachByCategory' => FormAttachByCategory::class
            ]
        ],
        'formrow' => [
            'class' => Formrow::class,
            'table' => 'filecabinets__formrows',
            'controllers' => [
                'index' => FormrowIndexController::class,
                'create' => FormrowCreateStoreController::class,
                'store' => FormrowCreateStoreController::class,
                'show' => FormrowShowController::class,
                'edit' => FormrowEditUpdateController::class,
                'update' => FormrowEditUpdateController::class,
                'destroy' => FormrowDestroyController::class,
            ],
            'fieldsGroupsFiles' => [
                'index' => FormrowFieldsGroupParametersFile::class,
                'related' => FormrowRelatedFieldsGroupParametersFile::class
            ],
            'parametersFiles' => [
                'create' => FormrowCreateStoreFieldsetsParameters::class,
                'edit' => FormrowEditFieldsetsParameters::class,
                'show' => FormrowShowFieldsetsParameters::class
            ],
        ],
        'dossier' => [
            'class' => Dossier::class,
            'table' => 'filecabinets__dossiers',
            'fieldsGroupsFiles' => [
                'index' => DossierFieldsGroupParametersFile::class,
                'related' => DossierRelatedFieldsGroupParametersFile::class
            ],
            'controllers' => [
                'index' => DossierIndexController::class,
                'update' => DossierUpdateController::class,
                'updateFields' => DossierUpdateFieldsController::class,
                'createNewInstance' => DossierCreateNewInstanceController::class,
                'destroy' => DossierDestroyController::class,
            ],
        ],
        'dossierrow' => [
            'class' => Dossierrow::class,
            'table' => 'filecabinets__dossierrows',
            'controllers' => [
                'show' => DossierrowShowController::class,
                'addInstance' => DossierrowAddInstanceController::class,
            ],
        ],
        'dossierFilecabinet' => [
            'class' => DossierFilecabinet::class,
            'table' => 'filecabinets___dossier_filecabinets',
        ],
        'filecabinet' => [
            'class' => Filecabinet::class,
            'table' => 'filecabinets__filecabinets',
            'controllers' => [
                'populate' => FilecabinetPopulateController::class,
                'show' => FilecabinetShowController::class,
                'pdf' => FilecabinetPdfController::class,
            ],

        ],
        // 'accessory' => [
        //     'class' => Accessory::class,
        //     'table' => 'products__accessories',
        //     'fieldsGroupsFiles' => [
        //         'index' => AccessoryFieldsGroupParametersFile::class
        //     ],
        //     'relationshipsManagerClasses' => [
        //         'show' => ProductRelationManager::class
        //     ],
        //     'parametersFiles' => [
        //         'crud' => AccessoryCrudFieldsetsParameters::class,
        //     ],
        //     'controllers' => [
        //         'crud' => AccessoryCrudController::class,
        //         'media' => AccessoryMediaController::class,
        //         'create' => AccessoryCreateStoreController::class,
        //         'edit' => AccessoryEditUpdateController::class,
        //         'destroy' => AccessoryDeletionController::class,
        //         'index' => AccessoryIndexController::class,
        //         'byOrderProductIndex' => ByOrderProductIndexController::class,
        //         'current' => ProductCurrentController::class
        //     ],
        // ]
    ]
];