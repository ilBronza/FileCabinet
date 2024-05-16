<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossiers;

use IlBronza\FileCabinet\Helpers\DossierCreatorHelper;

class DossierUpdateFieldsController extends DossierCRUD
{
    public $allowedMethods = ['updateFields'];

    public function updateFields(string $dossier)
    {
        $dossier = $this->findModel($dossier);

        DossierCreatorHelper::updateDossierRowsByForm($dossier);

        return back();
    }
}
