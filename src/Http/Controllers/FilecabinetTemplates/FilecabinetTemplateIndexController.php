<?php

namespace IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates;

use IlBronza\CRUD\Http\Controllers\Traits\PackageStandardIndexTrait;
use IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates\FilecabinetTemplateCRUD;

class FilecabinetTemplateIndexController extends FilecabinetTemplateCRUD
{
    use PackageStandardIndexTrait;

    public $allowedMethods = ['index'];

    public function getIndexElementsRelationsArray() : array
    {
        return [];
    }

}
