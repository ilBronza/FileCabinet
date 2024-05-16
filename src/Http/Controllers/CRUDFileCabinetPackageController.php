<?php

namespace IlBronza\FileCabinet\Http\Controllers;

use IlBronza\CRUD\CRUD;

class CRUDFileCabinetPackageController extends CRUD
{
    public function getEditParametersFile() : ? string
    {
        return config("filecabinet.models.{$this->configModelClassName}.parametersFiles.edit");
    }

    public function getRouteBaseNamePrefix() : ? string
    {
        return config('filecabinet.routePrefix');
    }

    public function setModelClass()
    {
        $this->modelClass = config("filecabinet.models.{$this->configModelClassName}.class");
    }
}
