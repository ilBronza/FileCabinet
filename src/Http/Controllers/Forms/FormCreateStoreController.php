<?php

namespace IlBronza\FileCabinet\Http\Controllers\Forms;

use IlBronza\CRUD\Traits\CRUDCreateStoreTrait;
use IlBronza\CRUD\Traits\CRUDRelationshipTrait;

class FormCreateStoreController extends FormCRUD
{
    use CRUDCreateStoreTrait;
    use CRUDRelationshipTrait;

    public $allowedMethods = [
        'create',
        'store',
    ];

    public function getGenericParametersFile() : ? string
    {
        return config("filecabinet.models.{$this->configModelClassName}.parametersFiles.create");
    }
}
