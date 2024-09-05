<?php

namespace IlBronza\FileCabinet\Http\Controllers\Forms;

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
