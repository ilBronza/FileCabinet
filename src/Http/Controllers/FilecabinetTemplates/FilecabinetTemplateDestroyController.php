<?php

namespace IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates;

use IlBronza\CRUD\Traits\CRUDDeleteTrait;

class FilecabinetTemplateDestroyController extends FilecabinetTemplateCRUD
{
    use CRUDDeleteTrait;

    public $allowedMethods = ['destroy'];

    public function destroy($filecabinetTemplate)
    {
        $filecabinetTemplate = $this->findModel($filecabinetTemplate);

        return $this->_destroy($filecabinetTemplate);
    }
}