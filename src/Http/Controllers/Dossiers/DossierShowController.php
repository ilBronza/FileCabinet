<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossiers;

use IlBronza\CRUD\Traits\CRUDRelationshipTrait;
use IlBronza\CRUD\Traits\CRUDShowTrait;
use IlBronza\FileCabinet\Helpers\DossierPopulatorHelper;
use Illuminate\Http\Request;

class DossierShowController extends DossierCRUD
{
    use CRUDShowTrait;
    use CRUDRelationshipTrait;

    public $allowedMethods = ['show'];

    public function getGenericParametersFile() : ? string
    {
        return config('filecabinet.models.dossier.parametersFiles.show');
    }

    public function getRelationshipsManagerClass()
    {
        return config("filecabinet.models.{$this->configModelClassName}.relationshipsManagerClasses.show");
    }

    public function show(string $dossier)
    {
        $dossier = $this->findModel($dossier);

        return $this->_show($dossier);
    }
}
