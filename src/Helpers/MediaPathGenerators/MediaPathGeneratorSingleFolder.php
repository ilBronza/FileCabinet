<?php

namespace IlBronza\FileCabinet\Helpers\MediaPathGenerators;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function get_class_methods;
use function ucfirst;

class MediaPathGeneratorSingleFolder extends FilecabinetMediaPathGenerator
{
    /*
     * Get a unique base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
		$dossierrow = $media->model()->first();
		return $dossierrow->getFormrow()->getModel()->getSpecialParameter('folderName', []);
	}
}
