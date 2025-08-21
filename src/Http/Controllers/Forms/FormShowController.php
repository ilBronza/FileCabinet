<?php

namespace IlBronza\FileCabinet\Http\Controllers\Forms;

use IlBronza\CRUD\Traits\CRUDRelationshipTrait;
use IlBronza\CRUD\Traits\CRUDShowTrait;

class FormShowController extends FormCRUD
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

    public function show(string $form)
    {
        $form = $this->findModel($form);

        return $this->_show($form);
    }
}
