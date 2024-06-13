<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossiers;

use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDPlainIndexTrait;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierCRUD;

class DossierIndexController extends DossierCRUD
{
    use CRUDPlainIndexTrait;
    use CRUDIndexTrait;

    public $allowedMethods = ['index'];

    public function getIndexFieldsArray()
    {
        //DossierFieldsGroupParametersFile
        return config('filecabinet.models.dossier.fieldsGroupsFiles.index')::getFieldsGroup();
    }

    public function getRelatedFieldsArray()
    {
        //DossierRelatedFieldsGroupParametersFile
        return config('filecabinet.models.dossier.fieldsGroupsFiles.related')::getFieldsGroup();
    }

    public function getIndexElements()
    {
        return $this->getModelClass()::with(
                    'filecabinets',
                    'form',
                    'dossierable'
                    // 'dossierrows.formrow'
                )
                ->get();
    }

}
