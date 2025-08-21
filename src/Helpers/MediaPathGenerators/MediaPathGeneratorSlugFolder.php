<?php

namespace IlBronza\FileCabinet\Helpers\MediaPathGenerators;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function get_class_methods;
use function ucfirst;

class MediaPathGeneratorSlugFolder extends FilecabinetMediaPathGenerator
{
    /*
     * Get a unique base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
		$dossierrow = $media->model()->first();
		$formRow = $dossierrow->getFormrow();

		$folderName = $formRow->getModel()->getSpecialParameter('folderName', null);

		if($folderName)
			return $folderName . '_' . $formRow->getSlug();

		return $formRow->getSlug();
	}
}
