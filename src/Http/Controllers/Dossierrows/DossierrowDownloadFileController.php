<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossierrows;

use IlBronza\FileCabinet\Helpers\MediaNameGenerators\FilecabinetDownloadFileNamer;
use IlBronza\FileCabinet\Http\Controllers\Dossierrows\DossierrowCRUD;
use Illuminate\Support\Facades\Storage;

class DossierrowDownloadFileController extends DossierrowCRUD
{
    public $allowedMethods = ['downloadFile'];

    public function getDisk() : string
    {
        return config('filecabinet.fileRowsDisk');
    }

    public function downloadFile(string $dossierrow)
    {
        $dossierrow = $this->findModel($dossierrow);

        $downloadFilename = FilecabinetDownloadFileNamer::getFilenameByDossierrow($dossierrow);

        if(! Storage::disk($this->getDisk())->exists($dossierrow->file))
            abort(404);

        return response()->download(
            Storage::disk($this->getDisk())->path($dossierrow->file),
            $downloadFilename
        );
    }
}
