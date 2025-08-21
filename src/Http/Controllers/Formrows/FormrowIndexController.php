<?php

namespace IlBronza\FileCabinet\Http\Controllers\Formrows;

use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDPlainIndexTrait;
use IlBronza\FileCabinet\Http\Controllers\Formrows\FormrowCRUD;

use function config;

class FormrowIndexController extends FormrowCRUD
{
    use CRUDPlainIndexTrait;
    use CRUDIndexTrait;

    public $allowedMethods = ['index'];
	public $avoidCreateButton = true;

    public function getIndexFieldsArray()
    {
		//FormrowFieldsGroupParametersFile
        return config('filecabinet.models.formrow.fieldsGroupsFiles.index')::getFieldsGroup();
    }

    public function getRelatedFieldsArray()
    {
        return config('filecabinet.models.formrow.fieldsGroupsFiles.related')::getFieldsGroup();
    }

    public function getIndexElements()
    {
        return $this->getModelClass()::with('form')->get();
    }

}
