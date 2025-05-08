<?php

namespace IlBronza\FileCabinet\Http\Controllers\Forms;

use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDPlainIndexTrait;
use IlBronza\FileCabinet\Http\Controllers\Forms\FormCRUD;

class FormMoveIndexController extends FormIndexController
{
    public $allowedMethods = ['index'];

    public function getIndexFieldsArray()
    {
        //FormFieldsGroupParametersFile
        return config('filecabinet.models.form.fieldsGroupsFiles.moveIndex')::getFieldsGroup();
    }
}
