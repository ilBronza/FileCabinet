<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossiers;

use IlBronza\FileCabinet\Helpers\DossierPopulatorHelper;
use Illuminate\Http\Request;

class DossierUpdateController extends DossierCRUD
{
    public $allowedMethods = ['update'];

    public function update(Request $request, string $dossier)
    {
        $dossier = $this->findModel($dossier);

        DossierPopulatorHelper::populateDossierByRequest($request, $dossier);

        return response()->success();
    }
}
