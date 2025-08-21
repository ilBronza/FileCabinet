<?php

namespace IlBronza\FileCabinet\Http\Controllers\Filecabinets;

use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDPlainIndexTrait;
use IlBronza\FileCabinet\Http\Controllers\Filecabinets\FilecabinetCRUD;

class FilecabinetIndexController extends FilecabinetCRUD
{
    use CRUDPlainIndexTrait;
    use CRUDIndexTrait;

    public $allowedMethods = ['index'];

    public function getIndexFieldsArray()
    {
        return config('filecabinet.models.filecabinet.fieldsGroupsFiles.index')::getFieldsGroup();
    }

    public function getRelatedFieldsArray()
    {
        return config('filecabinet.models.filecabinet.fieldsGroupsFiles.related')::getFieldsGroup();
    }

    public function getIndexElements()
    {
        return $this->getModelClass()::all();
    }

}
