<?php

namespace IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates;

use IlBronza\CRUD\Traits\CRUDRelationshipTrait;
use IlBronza\CRUD\Traits\CRUDShowTrait;

class FilecabinetTemplateShowController extends FilecabinetTemplateCRUD
{
    use CRUDShowTrait;
    use CRUDRelationshipTrait;

    public $allowedMethods = ['show'];

    public function getGenericParametersFile() : ? string
    {
        return config('filecabinetTemplates.models.filecabinetTemplate.parametersFiles.create');
    }

    public function getRelationshipsManagerClass()
    {
        return config("filecabinetTemplates.models.{$this->configModelClassName}.relationshipsManagerClasses.show");
    }

    public function show(string $filecabinetTemplate)
    {
        $filecabinetTemplate = $this->findModel($filecabinetTemplate);

        return $this->_show($filecabinetTemplate);
    }
}
