<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossierrows;

use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDPlainIndexTrait;
use IlBronza\FileCabinet\Http\Controllers\Dossierrows\DossierrowCRUD;

class FileDossierrowIndexController extends DossierrowCRUD
{
    use CRUDPlainIndexTrait;
    use CRUDIndexTrait;

    public $allowedMethods = ['index'];

    public $avoidCreateButton = true;

    public function getIndexFieldsArray()
    {
        //DossierrowFilessIndexFieldsGroupParametersFile
        return config('filecabinet.models.dossierrow.fieldsGroupsFiles.filesIndex')::getTracedFieldsGroup();
    }

    public function getIndexElements()
    {
        return $this->getModelClass()::with(
            'formrow.form',
            'dossier.client'
        )->whereHas('formrow', function($query)
        {
            $query->where('type', 'file');
        })->where('compiled', 0)->whereNotNull('file')->get();
    }

}
