<?php

namespace IlBronza\FileCabinet\Helpers\MediaPathGenerators;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

use function get_class_methods;
use function ucfirst;

class FilecabinetMediaPathGenerator extends DefaultPathGenerator
{
    /*
     * Get a unique base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
		$dossierrow = $media->model()->first();

		if(! $folderType = $dossierrow->getFormrow()->getSpecialParameter('folderType', null))
			throw new \Exception('folderType not set in formrow management panel');

		$helperClassname = config('filecabinet.media.mediaPathGenerators.' . $folderType);

		$folderName = (new $helperClassname)->getBasePath($media);

		$prefix = $dossierrow->getFormrow()->getSpecialParameter('folderPrefix', null);
		$suffix = $dossierrow->getFormrow()->getSpecialParameter('folderSuffix', null);

		return "{$prefix}{$folderName}{$suffix}";
	}
}
