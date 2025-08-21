<?php

namespace IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates;

use IlBronza\CRUD\Traits\CRUDEditUpdateTrait;
use Illuminate\Http\Request;

class FilecabinetTemplateEditUpdateController extends FilecabinetTemplateCRUD
{
    use CRUDEditUpdateTrait;

    public $allowedMethods = ['edit', 'update'];

    public function getGenericParametersFile() : ? string
    {
        return config('filecabinet.models.filecabinetTemplate.parametersFiles.create');
    }

    public function edit(string $filecabinetTemplate)
    {
        $filecabinetTemplate = $this->findModel($filecabinetTemplate);

        return $this->_edit($filecabinetTemplate);
    }

    public function update(Request $request, $filecabinetTemplate)
    {
        $filecabinetTemplate = $this->findModel($filecabinetTemplate);

        return $this->_update($request, $filecabinetTemplate);
    }
}
