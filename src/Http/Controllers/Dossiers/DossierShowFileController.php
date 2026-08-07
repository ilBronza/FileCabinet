<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossiers;

use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierCRUD;
use Illuminate\Support\Facades\Storage;

class DossierShowFileController extends DossierCRUD
{
    public $allowedMethods = ['showFiles'];

    public function showFiles(string $dossier)
    {
        $dossier = $this->findModel($dossier);

        if(! count($files = $dossier->getFilesDossierrows()))
            abort(404);

        if(count($files) > 1)
            return redirect()->to(
                $dossier->getDownloadFilesUrl()
            );

        return redirect()->to(
            $files->first()->getShowFileUrl()
        );
    }
}
