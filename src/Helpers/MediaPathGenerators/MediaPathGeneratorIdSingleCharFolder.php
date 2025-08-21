<?php

namespace IlBronza\FileCabinet\Helpers\MediaPathGenerators;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function get_class_methods;
use function implode;
use function str_split;
use function ucfirst;

class MediaPathGeneratorIdSingleCharFolder extends FilecabinetMediaPathGenerator
{
    /*
     * Get a unique base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
		return implode("/", str_split($media->getKey()));
    }
}
