te<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Form;
use Illuminate\Database\Eloquent\Model;

class AttachDossierToModelHelper
{
    static function attachByFormModel(Form $form, Model $model, Dossier $parentDossier = null)
    {
        return DossierCreatorHelper::createByForm(
            $model,
            $form
        );
    }

}