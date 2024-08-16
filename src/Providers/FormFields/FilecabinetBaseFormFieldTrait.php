<?php

namespace IlBronza\FileCabinet\Providers\FormFields;

use IlBronza\FileCabinet\Helpers\DossierCreatorHelper;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Form;

trait FilecabinetBaseFormFieldTrait
{
	public function getViewName($type) : string
	{
		return "filecabinet::formFields." . static::$viewFilename;
	}

	public function getShowViewName($type) : string
	{
		return "filecabinet::formFields.show." . static::$viewFilename;
	}

	public function getFormSlug() : string
	{
		return $this->formSlug;
	}
	public function getDossier() : Dossier
	{
		if(isset($this->dossier))
			return $this->dossier;

		dd('manga il dossiero');
		return DossierCreatorHelper::getOrCreateByForm(
			$this->getModel(),
			$this->getFilecabinetForm()
		);
	}

	/**
	 * @return mixed
	 */
	public function getFilecabinetForm() : Form
	{
		return Form::where('slug', $this->getFormSlug())->first();
	}
}