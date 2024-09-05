<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossierrows;

use IlBronza\CRUD\Traits\CRUDRelationshipTrait;
use IlBronza\CRUD\Traits\CRUDShowTrait;
use IlBronza\FileCabinet\Http\Controllers\Dossierrows\DossierrowCRUD;

class DossierrowShowController extends DossierrowCRUD
{
    use CRUDShowTrait;
    use CRUDRelationshipTrait;

    public $allowedMethods = ['show'];

    public function getGenericParametersFile() : ? string
    {
        //FormShowFieldsetsParameters
        return config("filecabinet.models.{$this->configModelClassName}.parametersFiles.show");
    }

    public function getRelationshipsManagerClass()
    {
        //FormRelationManager
        return config("filecabinet.models.{$this->configModelClassName}.relationshipsManagerClasses.show");
    }

    public function show(string $dossierrow)
    {
        $dossierrow = $this->findModel($dossierrow);

        return $this->_show($dossierrow);
    }
}
