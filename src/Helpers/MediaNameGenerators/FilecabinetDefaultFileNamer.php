<?php

namespace IlBronza\FileCabinet\Helpers\MediaNameGenerators;


use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Formrow;
use Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer;
use Symfony\Component\HttpFoundation\File\File;

abstract class FilecabinetDefaultFileNamer extends DefaultFileNamer
{
	public Dossierrow $dossierrow;
	public Formrow $formrow;
	public File $file;

	abstract protected function _getBaseFilename() : string;

	public function getFile() : File
	{
		return $this->file;
	}

	public function setFile(File $file) : void
	{
		$this->file = $file;
	}

	public function getFileExtension() : string
	{
		return $this->getFile()->extension();
	}

	public function __construct(Dossierrow $dossierrow, Formrow $formrow, File $file)
	{
		$this->setDossierrow($dossierrow);
		$this->setFormrow($formrow);
		$this->setFile($file);
	}

	public function getDossierrow() : Dossierrow
	{
		return $this->dossierrow;
	}

	public function setDossierrow(Dossierrow $dossierrow) : void
	{
		$this->dossierrow = $dossierrow;
	}

	public function getFormrow() : Formrow
	{
		return $this->formrow;
	}

	public function setFormrow(Formrow $formrow) : void
	{
		$this->formrow = $formrow;
	}

	public function getDossier() : Dossier
	{
		if(isset($this->dossier))
			return $this->dossier;

		$this->dossier = $this->getDossierrow()->getDossier();

		return $this->dossier;
	}

	public function generateName(): string
	{
		return "{$this->getBaseFilename()}.{$this->getFileExtension()}";
	}

	protected function getBaseFilename() : string
	{
		$baseFilename = $this->_getBaseFilename();

		$prefix = $this->getFormrow()->getSpecialParameter('filePrefix', null);
		$suffix = $this->getFormrow()->getSpecialParameter('fileSuffix', null);

		return "{$prefix}{$baseFilename}{$suffix}";
	}
}
