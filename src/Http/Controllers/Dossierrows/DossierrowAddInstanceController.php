<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossierrows;

use IlBronza\FileCabinet\Helpers\DossierCreatorHelper;
use IlBronza\FileCabinet\Helpers\DossierrowFormFieldHelper;
use IlBronza\FileCabinet\Http\Controllers\Dossierrows\DossierrowCRUD;

class DossierrowAddInstanceController extends DossierrowCRUD
{
    public $allowedMethods = ['addInstance'];

    public function addInstance(string $dossierrow)
    {
        $dossierrow = $this->findModel($dossierrow);

        if(! $dossierrow->isRepeatable())
            throw new \Exception('Dossierrow is not repeatable');

        $newDossierrow = DossierCreatorHelper::replicateDossierrow($dossierrow);

        $forField = DossierrowFormFieldHelper::createFieldFromDossierrow($newDossierrow);

        return $forField->render();
    }
}
