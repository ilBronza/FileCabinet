<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Form;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Helpers\FormFieldsProvider\FormFieldsProvider;
use IlBronza\FormField\Helpers\FormFieldsProvider\FormfieldParametersHelper;
use Illuminate\Database\Eloquent\Model;

class DossierrowProviderHelper
{
    static function getFromModelFormFormrow(Model $model, Form $form, Formrow $formrow) : ? Dossierrow
    {
	    if(! $dossier = $model->dossiers()->where('form_id', $form->getKey())->latest()->first())
		    return null;

	    if(! $dossierrow = $dossier->dossierrows()->where('formrow_id', $formrow->getKey())->first())
		    return null;

		return $dossierrow;
    }
}