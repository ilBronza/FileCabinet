<?php

namespace IlBronza\FileCabinet\Http\Controllers\Forms;

use IlBronza\CRUD\Helpers\ModelManagers\CrudModelClonerHelper;
use Illuminate\Http\Request;

class FormCloneController extends FormCRUD
{
    public $allowedMethods = ['clone'];

    public function clone($form)
    {
        $form = $this->findModel($form);

        $cloned = CrudModelClonerHelper::clone($form);

        return redirect()->to(
            $cloned->getEditUrl()
        );
    }
}
