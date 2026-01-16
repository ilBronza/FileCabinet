<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossiers;

use IlBronza\FileCabinet\Helpers\MediaNameGenerators\FilecabinetDownloadFileNamer;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierCRUD;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class DossierDownloadFileController extends DossierCRUD
{
    public $allowedMethods = ['downloadFiles'];

    public function getDisk() : string
    {
        return config('filecabinet.fileRowsDisk');
    }

    public function downloadFiles(string $dossier)
    {
        $dossier = $this->findModel($dossier);

        if(! count($dossierrows = $dossier->getFilesDossierrows()))
            abort(404);

        $files = [];

        foreach ($dossierrows as $dossierrow)
        {
            if(! Storage::disk($this->getDisk())->exists($dossierrow->getFilePath()))
                continue;

            $files[] = [
                'path' => Storage::disk($this->getDisk())->path($dossierrow->getFilePath()),
                'name' => $dossierrow->getKey() . '_' . FilecabinetDownloadFileNamer::getFilenameByDossierrow($dossierrow)
            ];
        }

        $zipFileName = Str::slug($dossier->getDossierable()->getName() . '-' . $dossier->getName() . 'files_' . now()->format('Ymd_His')) . '.zip';

        $zipPath = storage_path("app/temp/{$zipFileName}");

        if (! file_exists(dirname($zipPath)))
            mkdir(dirname($zipPath), 0775, true);

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true)
        {
            foreach ($files as $file)
                $zip->addFile(
                    $file['path'],
                    basename($file['name'])
                );

            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
