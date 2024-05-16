<?php

namespace IlBronza\FileCabinet\Http\Controllers\Formrows;

use IlBronza\CRUD\Traits\CRUDRelationshipTrait;
use IlBronza\CRUD\Traits\CRUDShowTrait;

class FormrowShowController extends FormrowCRUD
{
    use CRUDShowTrait;
    use CRUDRelationshipTrait;

    public $allowedMethods = ['show'];

    public function getGenericParametersFile() : ? string
    {
        return config("filecabinet.models.{$this->configModelClassName}.parametersFiles.show");
    }

    public function getRelationshipsManagerClass()
    {
        return config("filecabinet.models.{$this->configModelClassName}.relationshipsManagerClasses.show");
    }

    // public function setShowButtons()
    // {
    //     $this->showButtons[] = $this->getModel()->getCreateFormrowButton();
    // }

    public function show(string $form)
    {
        $form = $this->findModel($form);

        return $this->_show($form);
    }
}
