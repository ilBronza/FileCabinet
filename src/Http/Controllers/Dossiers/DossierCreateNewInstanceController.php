<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossiers;

use IlBronza\FileCabinet\Helpers\DossierCreatorHelper;

class DossierCreateNewInstanceController extends DossierCRUD
{
    public $allowedMethods = ['createNewInstance'];

    public function createNewInstance(string $dossier)
    {
        $dossier = $this->findModel($dossier, ['dossierrows.formrow', 'form']);

        if(! $dossier->isRepeatable())
            throw new  \Exception(__('filecabinet::forms.dossier_not_repeatable'));

        $newDossier = DossierCreatorHelper::createByDossier($dossier);

        return back();
    }
}
