<?php

namespace IlBronza\FileCabinet\Http\Controllers\Formrows;

use function request;

class FormrowCondenseIndexController extends FormrowIndexController
{
    public function getIndexFieldsArray()
    {
		//FormrowFieldsGroupParametersFile
        return config('filecabinet.models.formrow.fieldsGroupsFiles.condense')::getFieldsGroup();
    }

    public function getIndexElements()
    {
		if(! request()->formrow)
			throw new \Exception('formrow not set');

        return $this->getModelClass()::with('form')->where('id', '!=', request()->formrow)->get();
    }

}
