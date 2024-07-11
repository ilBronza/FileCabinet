<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\FileCabinet\Models\Filecabinet;

class FilecabinetGetTreeHelper
{
	static function getDescendantTreeWithForms(Filecabinet $filecabinet)
	{
		return Filecabinet::getProjectClassname()::getFullTreeWithRelatedElements($filecabinet->getKey(), ['dossiers']);
	}
}