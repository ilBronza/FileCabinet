<?php

namespace IlBronza\FileCabinet\Http\Controllers\Forms;

use IlBronza\Buttons\Button;
use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDPlainIndexTrait;
use IlBronza\FileCabinet\Http\Controllers\Forms\FormCRUD;

class FormIndexController extends FormCRUD
{
    use CRUDPlainIndexTrait;
    use CRUDIndexTrait;

    public $allowedMethods = ['index'];

    public function addIndexButtons()
    {
        $this->table->addButton(Button::create([
            'href' => route(config('filecabinet.routePrefix') . 'forms.pdfOrder'),
            'text' => 'filecabinet::buttons.pdfFormsOrder',
            'icon' => 'file-pdf',
        ]));
    }

    public function getIndexFieldsArray()
    {
        //FormFieldsGroupParametersFile
        return config('filecabinet.models.form.fieldsGroupsFiles.index')::getTracedFieldsGroup();
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
