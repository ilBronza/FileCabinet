<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\FileCabinet\Models\Filecabinet;

class FilecabinetGetTreeHelper
{
	static function getDescendantTreeWithForms(Filecabinet $filecabinet)
	{
		return Filecabinet::getProjectClassName()::getFullTreeWithRelatedElements($filecabinet->getKey(), ['dossiers']);
	}
}