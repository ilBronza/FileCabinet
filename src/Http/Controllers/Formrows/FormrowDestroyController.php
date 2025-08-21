<?php

namespace IlBronza\FileCabinet\Http\Controllers\Formrows;

use IlBronza\CRUD\Traits\CRUDDeleteTrait;

class FormrowDestroyController extends FormrowCRUD
{
    use CRUDDeleteTrait;

    public $allowedMethods = ['destroy'];

    public function destroy($formrow)
    {
        $formrow = $this->findModel($formrow);

        return $this->_destroy($formrow);
    }
}