<?php

namespace IlBronza\FileCabinet\Helpers\MediaNameGenerators;

use IlBronza\FileCabinet\Models\Dossierrow;
use Illuminate\Support\Str;

class FilecabinetDownloadFileNamer
{
	static function getFilenameByDossierrow(Dossierrow $dossierrow) : ? string
	{
		if(! $filePath = $dossierrow->file)
			return null;

		$dossier = $dossierrow->getDossier();

		$subject = $dossier?->getDossierable();
		$form = $dossier?->getForm();
		$datarow = $dossierrow?->getFormrow();

		$pieces = [
			$subject->getName(),
			$form->getName(),
			$datarow->getName()
		];

		$extension = pathinfo($filePath)['extension'];

		return Str::slug(implode(" ", $pieces)) . '.' . $extension;
	}
}
