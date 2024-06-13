<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossiers;

use IlBronza\CRUD\Traits\CRUDEditUpdateTrait;
use IlBronza\FileCabinet\Helpers\DossierPopulatorHelper;
use Illuminate\Http\Request;

class DossierEditController extends DossierCRUD
{
    use CRUDEditUpdateTrait;
    use CRUDRelationshipTrait;

    public $allowedMethods = ['edit'];

    public function edit(string $dossier)
    {
        $dossier = $this->findModel($dossier);

        dd($dossier);

        // DossierPopulatorHelper::populateDossierByRequest($request, $dossier);

        return response()->success();
    }
}
