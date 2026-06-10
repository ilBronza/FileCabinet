<?php

namespace IlBronza\FileCabinet\Providers\DatatablesFields;

use IlBronza\CRUD\Models\Media;
use IlBronza\Datatables\DatatablesFields\DatatableFieldBoolean;
use IlBronza\FileCabinet\Helpers\DossierrowFileHelper;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Formrow;
use Illuminate\Support\Facades\Storage;

class DatatableFieldFileExists extends DatatableFieldBoolean
{
	public bool $nullable = true;

	public string $formName;
	public string $formrowName;

	public $requireElement = true;

	public function transformValue($element)
	{
		if (! $element instanceof Dossierrow)
			$element = $element->getDossierRowByNames($this->formName, $this->formrowName);

		if (! $element)
			return parent::transformValue(null);

		return parent::transformValue(
			static::fileExistsOnFilesystem($element)
		);
	}

	/**
	 * null  = non compilato
	 * false = compilato ma assente sul disk
	 * true  = compilato e presente
	 */
	protected static function fileExistsOnFilesystem(Dossierrow $dossierrow) : ?bool
	{
		if (! $dossierrow->isFileType())
			return null;

		$storage = static::resolveFileStorage($dossierrow);

		if ($storage === null)
			return null;

		return Storage::disk($storage['disk'])->exists($storage['path']);
	}

	/**
	 * @return array{path: string, disk: string}|null
	 */
	protected static function resolveFileStorage(Dossierrow $dossierrow) : ?array
	{
		$path = DossierrowFileHelper::getFilePath($dossierrow);

		if ($path === null)
			return null;

		return [
			'path' => $path,
			'disk' => static::resolveStorageDisk($dossierrow, $path),
		];
	}

	protected static function resolveStorageDisk(Dossierrow $dossierrow, string $path) : string
	{
		$reference = $dossierrow->getValue();

		if (is_string($reference) && ($media = Media::find(trim($reference))))
			return $media->disk;

		$media = $dossierrow->getLastMedia($dossierrow->getKey());

		if ($media && $media->getPathRelativeToRoot() === $path)
			return $media->disk;

		return $dossierrow->getFormrow()->getSpecialParameter('disk')
			?? config('filecabinet.fileRowsDisk')
			?? config('media-library.disk_name');
	}
}
