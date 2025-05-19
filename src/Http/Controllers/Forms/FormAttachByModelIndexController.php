<?php

namespace IlBronza\FileCabinet\Http\Controllers\Forms;

use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDPlainIndexTrait;

class FormAttachByModelIndexController extends FormIndexController
{
    use CRUDPlainIndexTrait;
    use CRUDIndexTrait;

    public $allowedMethods = ['index'];

    public function getIndexFieldsArray()
    {
        //FormFieldsGroupParametersFile
        return config('filecabinet.models.form.fieldsGroupsFiles.index')::getFieldsGroup();
    }

    public function getIndexElements()
    {
        return $this->getModelClass()::with(
            'formrows',
            'category',
            'parent',
            'children'
        )->withCount('dossiers')->get();
    }

}
