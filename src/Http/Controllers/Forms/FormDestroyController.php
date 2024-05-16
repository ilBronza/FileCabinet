<?php

namespace IlBronza\FileCabinet\Http\Controllers\Forms;

use IlBronza\CRUD\Traits\CRUDDeleteTrait;

class FormDestroyController extends FormCRUD
{
    use CRUDDeleteTrait;

    public $allowedMethods = ['destroy'];

    public function destroy($form)
    {
        $form = $this->findModel($form);

        return $this->_destroy($form);
    }
}