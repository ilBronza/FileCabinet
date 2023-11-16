<?php

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
    'routePrefix' => 'ibFileCabinets',

    'models' => [
        'form' => [
            'class' => Form::class,
            'table' => 'filecabinets__forms',
        ],
        'formrow' => [
            'class' => Formrow::class,
            'table' => 'filecabinets__formrows',
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