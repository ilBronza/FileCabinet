<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\CRUD\Models\Media;
use IlBronza\FileCabinet\Models\Dossierrow;

class DossierrowFileHelper
{
	/**
	 * Percorso del file relativo alla root del disk di storage.
	 *
	 * Un solo media per riga. Risoluzione (in ordine):
	 * 1. colonna legacy `file`, se valorizzata
	 * 2. colonna del row type (`string` per le righe file): id media Spatie oppure path già persistito
	 * 3. ultimo media nella collection Spatie nominata con l'id del dossierrow (upload AJAX)
	 */
	public static function getFilePath(Dossierrow $dossierrow) : ?string
	{
		if (! $dossierrow->isFileType())
			return null;

		if ($path = static::getLegacyFileColumnPath($dossierrow))
			return $path;

		if ($path = static::getPathFromStringReference($dossierrow))
			return $path;

		return static::getPathFromMediaCollection($dossierrow);
	}

	protected static function getLegacyFileColumnPath(Dossierrow $dossierrow) : ?string
	{
		return static::normalizePath(
			$dossierrow->getAttributes()['file'] ?? null
		);
	}

	protected static function getPathFromStringReference(Dossierrow $dossierrow) : ?string
	{
		$reference = $dossierrow->getValue();

		if (! is_string($reference))
			return null;

		$reference = trim($reference);

		if ($reference === '')
			return null;

		if ($media = Media::find($reference))
			return $media->getPathRelativeToRoot();

		return static::normalizePath($reference);
	}

	protected static function getPathFromMediaCollection(Dossierrow $dossierrow) : ?string
	{
		$media = $dossierrow->getLastMedia(
			$dossierrow->getKey()
		);

		if (! $media)
			return null;

		return $media->getPathRelativeToRoot();
	}

	protected static function normalizePath(mixed $path) : ?string
	{
		if (! is_string($path))
			return null;

		$path = trim($path);

		if ($path === '')
			return null;

		return $path;
	}
}
