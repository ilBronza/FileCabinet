<?php

namespace IlBronza\FileCabinet\Http\Controllers\Formrows;

use Carbon\Carbon;
use IlBronza\CRUD\Traits\CRUDCreateStoreTrait;
use IlBronza\CRUD\Traits\CRUDRelationshipTrait;
use IlBronza\FileCabinet\Http\Controllers\Formrows\FormrowCRUD;
use IlBronza\FileCabinet\Models\Form;
use Illuminate\Http\Request;

class FormrowCreateStoreController extends FormrowCRUD
{
    use CRUDCreateStoreTrait;
    use CRUDRelationshipTrait;

    public $allowedMethods = [
        'createFromForm',
        'storeFromForm',
    ];

    public function getModelDefaultParameters() : array
    {
        return [
            'form_id' => $this->form->getKey()
        ];
    }

    public function createFromForm(Form $form)
    {
        $this->form = $form;

        return $this->create();
    }

    public function getStoreModelAction()
    {
        return app('filecabinet')->route('formrows.storeFromForm', ['form' => $this->form]);
    }

    public function storeFromForm(Request $request, Form $form)
    {
        $this->form = $form;

        return $this->store($request);
    }

    public function getGenericParametersFile() : ? string
    {
        //FormrowCreateStoreFieldsetsParameters
        return config("filecabinet.models.{$this->configModelClassName}.parametersFiles.create");
    }

    public function getRelationshipsManagerClass()
    {
        return config("filecabinet.models.{$this->configModelClassName}.relationshipsManagerClasses.show");
    }

    public function getAfterStoredRedirectUrl() : string
    {
        $model = $this->getModel();

        if($model->hasSpecialParameters())
            return $model->getEditUrl();

        return $model->getForm()->getShowUrl();
    }
}
