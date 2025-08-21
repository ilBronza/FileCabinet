<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Filecabinet;

class FilecabinetCompletionCheckerHelper
{
	static function hasAllCompletedDossiers(Filecabinet $filecabinet) : bool
	{
        foreach($filecabinet->getDossiers() as $dossier)
            if(! $dossier->isPopulated())
            	return false;

		return true;
	}
}