<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossierrows;

use IlBronza\FileCabinet\Helpers\DossierCreatorHelper;

class DossierrowCreateNewInstanceController extends DossierrowCRUD
{
    public $allowedMethods = ['createNewInstance'];

    public function createNewInstance(string $dossierrow)
    {
        $dossierrow = $this->findModel($dossierrow, ['formrow']);

        if(! $dossierrow->isRepeatable())
            throw new  \Exception(__('filecabinet::forms.dossierrow_not_repeatable'));

        DossierCreatorHelper::replicateDossierrow($dossierrow);

        return back();
    }
}
