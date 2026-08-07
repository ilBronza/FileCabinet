<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossierrows;

use IlBronza\FileCabinet\Http\Controllers\Dossierrows\DossierrowCRUD;
use Illuminate\Support\Facades\Storage;

class DossierrowShowFileController extends DossierrowCRUD
{
    public $allowedMethods = ['showFile'];

    public function getDisk() : string
    {
        return config('filecabinet.fileRowsDisk');
    }

    public function showFile(string $dossierrow)
    {
        $dossierrow = $this->findModel($dossierrow);

        if(! Storage::disk($this->getDisk())->exists($dossierrow->file))
            abort(404);

        return response()->file(
            Storage::disk($this->getDisk())->path($dossierrow->file)
        );
    }
}
