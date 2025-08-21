<?php

namespace IlBronza\FileCabinet\Http\Controllers\Formrows;

use IlBronza\CRUD\Traits\CRUDEditUpdateTrait;
use Illuminate\Http\Request;

class FormrowEditUpdateController extends FormrowCRUD
{
    use CRUDEditUpdateTrait;

	public ? bool $updateEditor = false;

    public $allowedMethods = ['edit', 'update'];

    public function edit(string $formrow)
    {
        $formrow = $this->findModel($formrow);

        return $this->_edit($formrow);
    }

    public function update(Request $request, $formrow)
    {
        $formrow = $this->findModel($formrow);

        return $this->_update($request, $formrow);
    }

    public function getAfterUpdatedRedirectUrl() : string
    {
        return $this->getModel()->getForm()->getShowUrl();
    }
}
