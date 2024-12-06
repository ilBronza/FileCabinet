<?php

use IlBronza\FileCabinet\Helpers\MediaNameGenerators\FilecabinetDossiervalueNamer;
use IlBronza\FileCabinet\Helpers\MediaNameGenerators\FilecabinetOriginalFileNameNamer;
use IlBronza\FileCabinet\Helpers\MediaPathGenerators\MediaPathGeneratorIdFolder;
use IlBronza\FileCabinet\Helpers\MediaPathGenerators\MediaPathGeneratorIdSingleCharFolder;
use IlBronza\FileCabinet\Helpers\MediaPathGenerators\MediaPathGeneratorNameFolder;
use IlBronza\FileCabinet\Helpers\MediaPathGenerators\MediaPathGeneratorSingleFolder;
use IlBronza\FileCabinet\Helpers\MediaPathGenerators\MediaPathGeneratorSlugFolder;
use IlBronza\FileCabinet\Http\Controllers\Dossierrows\DossierrowAddInstanceController;
use IlBronza\FileCabinet\Http\Controllers\Dossierrows\DossierrowCreateNewInstanceController;
use IlBronza\FileCabinet\Http\Controllers\Dossierrows\DossierrowDeleteMediaController;
use IlBronza\FileCabinet\Http\Controllers\Dossierrows\DossierrowIndexController;
use IlBronza\FileCabinet\Http\Controllers\Dossierrows\DossierrowShowController;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierByFormIndexController;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierByModelCategoryController;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierCreateNewInstanceController;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierDestroyController;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierEditController;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierIndexController;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierPopulateController;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierShowController;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierUpdateController;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierUpdateFieldsController;
use IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates\FilecabinetDeleteMediaController;
use IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates\FilecabinetTemplateCreateStoreController;
use IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates\FilecabinetTemplateDestroyController;
use IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates\FilecabinetTemplateEditUpdateController;
use IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates\FilecabinetTemplateIndexController;
use IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates\FilecabinetTemplatePdfTemplateController;
use IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates\FilecabinetTemplateShowController;
use IlBronza\FileCabinet\Http\Controllers\Filecabinets\FilecabinetIndexController;
use IlBronza\FileCabinet\Http\Controllers\Filecabinets\FilecabinetPdfController;
use IlBronza\FileCabinet\Http\Controllers\Filecabinets\FilecabinetPopulateController;
use IlBronza\FileCabinet\Http\Controllers\Filecabinets\FilecabinetShowController;
use IlBronza\FileCabinet\Http\Controllers\FormAttaching\FormAttachByCategory;
use IlBronza\FileCabinet\Http\Controllers\Formrows\FormrowCreateStoreController;
use IlBronza\FileCabinet\Http\Controllers\Formrows\FormrowDestroyController;
use IlBronza\FileCabinet\Http\Controllers\Formrows\FormrowEditUpdateController;
use IlBronza\FileCabinet\Http\Controllers\Formrows\FormrowIndexController;
use IlBronza\FileCabinet\Http\Controllers\Formrows\FormrowReorderController;
use IlBronza\FileCabinet\Http\Controllers\Formrows\FormrowShowController;
use IlBronza\FileCabinet\Http\Controllers\Forms\FormCloneController;
use IlBronza\FileCabinet\Http\Controllers\Forms\FormCreateStoreController;
use IlBronza\FileCabinet\Http\Controllers\Forms\FormDestroyController;
use IlBronza\FileCabinet\Http\Controllers\Forms\FormEditUpdateController;
use IlBronza\FileCabinet\Http\Controllers\Forms\FormIndexController;
use IlBronza\FileCabinet\Http\Controllers\Forms\FormShowController;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\DossierFilecabinet;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Filecabinet;
use IlBronza\FileCabinet\Models\FilecabinetTemplate;
use IlBronza\FileCabinet\Models\Form;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\FileCabinet\Providers\FieldsGroups\DossierByFormFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsGroups\DossierFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsGroups\DossierRelatedFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsGroups\DossierrowFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsGroups\DossierrowRelatedFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsGroups\FilecabinetFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsGroups\FilecabinetRelatedFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsGroups\FilecabinetTemplateFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsGroups\FormFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsGroups\FormrowByFormFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsGroups\FormrowFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsGroups\FormrowRelatedFieldsGroupParametersFile;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\DossierShowFieldsetsParameters;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\FilecabinetTemplateCreateStoreFieldsetsParameters;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\FilecabinetTemplatePdfTemplateFieldsetsParameters;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\FormCreateStoreFieldsetsParameters;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\FormEditUpdateFieldsetsParameters;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\FormShowFieldsetsParameters;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\FormrowCreateStoreFieldsetsParameters;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\FormrowEditFieldsetsParameters;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\FormrowShowFieldsetsParameters;
use IlBronza\FileCabinet\Providers\RelationshipsManagers\DossierRelationManager;
use IlBronza\FileCabinet\Providers\RelationshipsManagers\DossierrowRelationManager;
use IlBronza\FileCabinet\Providers\RelationshipsManagers\FilecabinetTemplateRelationManager;
use IlBronza\FileCabinet\Providers\RelationshipsManagers\FormRelationManager;
use IlBronza\FileCabinet\Providers\RelationshipsManagers\FormrowRelationManager;

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
    'enabled' => true,

    'updateEditor' => true,
    
    'routePrefix' => 'ibFilecabinet',

    'filecabinet' => [
        'buttons' => [
            'showPrintPartialPdf' => true
        ]
    ],

	'formfields' => [
		'status' => [
			'showMilestones' => true
		]
	],

    'defaultRules' => [
        'textarea' => [
            'max' => 2048
        ],
        'text' => [
            'max' => 255
        ]
    ],

	'media' => [
		'mediaPathGenerators' => [
			'singleFolder' => MediaPathGeneratorSingleFolder::class,
			'dossierrowIdFolder' => MediaPathGeneratorIdFolder::class,
			'formrowSlugFolder' => MediaPathGeneratorSlugFolder::class,
			'nameFolder' => MediaPathGeneratorNameFolder::class,
			'mediaIdSingleCharFolder' => MediaPathGeneratorIdSingleCharFolder::class,
		],

		'mediaNameGenerators' => [
			'originalFilename' => FilecabinetOriginalFileNameNamer::class,
			'dossierValueRules' => FilecabinetDossiervalueNamer::class,
		],
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
	            'attachByModel' => FormAttachByModelIndexController::class,
                'create' => FormCreateStoreController::class,
                'store' => FormCreateStoreController::class,
                'show' => FormShowController::class,
                'clone' => FormCloneController::class,
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
	            'reorder' => FormrowReorderController::class,
	            'index' => FormrowIndexController::class,
                'create' => FormrowCreateStoreController::class,
                'store' => FormrowCreateStoreController::class,
                'show' => FormrowShowController::class,
                'edit' => FormrowEditUpdateController::class,
                'update' => FormrowEditUpdateController::class,
                'destroy' => FormrowDestroyController::class,
            ],
            'relationshipsManagerClasses' => [
                'show' => FormrowRelationManager::class
            ],
            'fieldsGroupsFiles' => [
                'byForm' => FormrowByFormFieldsGroupParametersFile::class,
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
				'byForm' => DossierByFormFieldsGroupParametersFile::class,
                'related' => DossierRelatedFieldsGroupParametersFile::class
            ],
            'relationshipsManagerClasses' => [
                'show' => DossierRelationManager::class
            ],
            'parametersFiles' => [
                'show' => DossierShowFieldsetsParameters::class
            ],
            'controllers' => [
				'byModelCategory' => DossierByModelCategoryController::class,
				'populate' => DossierPopulateController::class,
	            'index' => DossierIndexController::class,
				'byForm' => DossierByFormIndexController::class,
                'show' => DossierShowController::class,
                'edit' => DossierEditController::class,
                'update' => DossierUpdateController::class,
                'updateFields' => DossierUpdateFieldsController::class,
                'createNewInstance' => DossierCreateNewInstanceController::class,
                'destroy' => DossierDestroyController::class,
            ],
        ],
        'dossierrow' => [
            'class' => Dossierrow::class,
            'table' => 'filecabinets__dossierrows',
            'fieldsGroupsFiles' => [
                'index' => DossierrowFieldsGroupParametersFile::class,
                'related' => DossierrowRelatedFieldsGroupParametersFile::class
            ],
            'relationshipsManagerClasses' => [
                'show' => DossierrowRelationManager::class
            ],
            'controllers' => [
                'show' => DossierrowShowController::class,
                'index' => DossierrowIndexController::class,
	            'createNewInstance' => DossierrowCreateNewInstanceController::class,
				'deleteMedia' => DossierrowDeleteMediaController::class,
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
            'fieldsGroupsFiles' => [
                'index' => FilecabinetFieldsGroupParametersFile::class,
                'related' => FilecabinetRelatedFieldsGroupParametersFile::class
            ],
            'controllers' => [
                'populate' => FilecabinetPopulateController::class,
                'show' => FilecabinetShowController::class,
                'index' => FilecabinetIndexController::class,
                'pdf' => FilecabinetPdfController::class,
            ],

        ],
        'filecabinetTemplate' => [
            'class' => FilecabinetTemplate::class,
            'table' => 'filecabinets__filecabinettemplates',
            'fieldsGroupsFiles' => [
                'index' => FilecabinetTemplateFieldsGroupParametersFile::class
            ],
            'relationshipsManagerClasses' => [
                'show' => FilecabinetTemplateRelationManager::class
            ],
            'parametersFiles' => [
                'create' => FilecabinetTemplateCreateStoreFieldsetsParameters::class,
                'pdfTemplate' => FilecabinetTemplatePdfTemplateFieldsetsParameters::class
            ],
            'controllers' => [
                'pdfTemplate' => FilecabinetTemplatePdfTemplateController::class,
	            'deleteMedia' => FilecabinetDeleteMediaController::class,
                'index' => FilecabinetTemplateIndexController::class,
                'create' => FilecabinetTemplateCreateStoreController::class,
                'store' => FilecabinetTemplateCreateStoreController::class,
                'show' => FilecabinetTemplateShowController::class,
                'edit' => FilecabinetTemplateEditUpdateController::class,
                'update' => FilecabinetTemplateEditUpdateController::class,
                'destroy' => FilecabinetTemplateDestroyController::class,
            ]
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