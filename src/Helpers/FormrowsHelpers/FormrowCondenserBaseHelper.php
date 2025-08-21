<?php

namespace IlBronza\FileCabinet\Helpers\FormrowsHelpers;

use IlBronza\FileCabinet\Helpers\DossierCreatorHelper;
use IlBronza\FileCabinet\Models\Form;
use IlBronza\FileCabinet\Models\Formrow;
use Illuminate\Support\Collection;

abstract class FormrowCondenserBaseHelper
{
	public ? Formrow $formrow = null;
	public ? Form $targetForm = null;
	public ? Formrow $targetRow = null;

	abstract public function closeFormrow();

	public function setFormrow(Formrow $formrow) : static
	{
		$this->formrow = $formrow;

		return $this;
	}

	public function setTargetForm(Form $targetForm) : static
	{
		$this->targetForm = $targetForm;

		return $this;
	}

	public function getForm() : ? Form
	{
		return $this->targetForm;
	}

	public function setTargetRow(Formrow $targetRow) : static
	{
		$this->targetRow = $targetRow;

		return $this;
	}

	public function getFormId()
	{
		if($this->targetForm)
			return $this->targetForm->getKey();

		return $this->targetRow->form_id;
	}

	public function getDossierrowsToConvert() : Collection
	{
		return $this->formrow->dossierrows()->with('dossier', function($query)
		{
			$query->with('dossierable', function($_query)
			{
				$_query->with('dossiers', function($__query)
				{
					$__query->where('form_id',  $this->getFormId());
				});
			});
		})->get();
	}

	public function parseDossierrows()
	{
		foreach($this->getDossierrowsToConvert() as $dossierrow)
		{
			if(! $dossier = $dossierrow->dossier)
			{
				$dossierrow->delete();

				continue;
			}

			if(! $dossierable = $dossier->dossierable)
			{
				$dossier->delete();

				continue;
			}

			if(! $newDossierId = $dossierable->dossiers?->first()?->getKey())
				$newDossierId = DossierCreatorHelper::createByForm(
					$dossierrow->dossier->dossierable,
					$this->targetRow->getForm()
				)->getKey();

			if($this->targetRow)
				$dossierrow->formrow_id = $this->targetRow->getKey();

			$dossierrow->dossier_id = $newDossierId;
			$dossierrow->save();
		}

		if(! $this->getForm())
		{
			$formrow->delete();

			return true;
		}

		$formrow->form_id = $this->getForm()->getKey();
		$formrow->save();

		return true;
	}
}