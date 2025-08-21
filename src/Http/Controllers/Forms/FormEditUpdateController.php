<?php

namespace IlBronza\FileCabinet\Http\Controllers\Forms;

use IlBronza\CRUD\Traits\CRUDEditUpdateTrait;
use Illuminate\Http\Request;

use function dd;

class FormEditUpdateController extends FormCRUD
{
    use CRUDEditUpdateTrait;

    public $allowedMethods = ['edit', 'update'];

    public function getGenericParametersFile() : ? string
    {
        //FormEditUpdateFieldsetsParameters
        return config("filecabinet.models.{$this->configModelClassName}.parametersFiles.edit");
    }

    public function edit(string $form)
    {
        $form = $this->findModel($form);

        return $this->_edit($form);
    }

    public function update(Request $request, $form)
    {
        $form = $this->findModel($form);

	    return $this->_update($request, $form);
    }
}
