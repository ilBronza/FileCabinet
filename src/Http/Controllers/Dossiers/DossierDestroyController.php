<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossiers;

use IlBronza\CRUD\Traits\CRUDDeleteTrait;

class DossierDestroyController extends DossierCRUD
{
    use CRUDDeleteTrait;

    public $allowedMethods = ['destroy'];

    public function destroy($dossier)
    {
        $dossier = $this->findModel($dossier);

        return $this->_destroy($dossier);
    }
}