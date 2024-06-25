<?php

namespace IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates;

use IlBronza\CRUD\Traits\CRUDCreateStoreTrait;
use IlBronza\CRUD\Traits\CRUDRelationshipTrait;
use IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates\FilecabinetTemplateCRUD;

class FilecabinetTemplateCreateStoreController extends FilecabinetTemplateCRUD
{
    use CRUDCreateStoreTrait;
    use CRUDRelationshipTrait;

    public $allowedMethods = [
        'create',
        'store',
    ];

    public function getGenericParametersFile() : ? string
    {
        return config("{$this->getPackageConfigName()}.models.{$this->getModelConfigPrefix()}.parametersFiles.create");
    }
}
