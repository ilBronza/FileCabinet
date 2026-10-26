<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Filecabinet;

class FilecabinetPdfTreeHelper
{
    public static function prepare(Filecabinet $filecabinet): void
    {
        $filecabinet->pdfRendering = true;
        $filecabinet->loadMissing(['dossiers.form', 'dossiers.dossierrows.formrow']);

        foreach ($filecabinet->getPdfDossiers() as $dossier) {
            static::prepareDossier($dossier);
        }

        // The menu traverses recursiveChildren, while the PDF body traverses children.
        $children = $filecabinet->getRecursiveChildren();
        $filecabinet->setRelation('children', $children);

        foreach ($children as $child) {
            static::prepare($child);
        }
    }

    protected static function prepareDossier(Dossier $dossier): void
    {
        $dossier->pdfRendering = true;
        $dossier->loadMissing('children.form');

        foreach ($dossier->getContentElements() as $child) {
            static::prepareDossier($child);
        }
    }
}
