<?php

namespace IlBronza\FileCabinet\Helpers\FormrowsHelpers;

use IlBronza\FileCabinet\Helpers\DossierCreatorHelper;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Dossierrow;
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

	public function getTargetForm() : ? Form
	{
		if($this->targetForm)
			return $this->targetForm;

		return $this->targetRow->getForm();
	}

	public function setTargetRow(Formrow $targetRow) : static
	{
		$this->targetRow = $targetRow;

		return $this;
	}

	public function getTargetFormId()
	{
		if($result = $this->getTargetForm())
			return $result->getKey();

		return $this->targetRow->form_id;
	}

	public function getDossierrowsToConvert() : Collection
	{
		return $this->formrow->dossierrows()
			->with('dossier', function($query)
			{
				$query->with('dossierable', function($_query)
				{
					$_query->with('dossiers', function($__query)
					{
						$__query->where('form_id',  $this->getTargetFormId());
					});
				});
			})->where('formrow_id', $this->formrow->getKey())
		    ->get();
	}

	public function getCleanedDossierrowsToConvert() : Collection
	{
		$result = collect();

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

			$result->push($dossierrow);
		}

		return $result;
	}

	public function getTargetDossierByDossierrow(Dossierrow $dossierrow)
	{
		$dossierable = $dossierrow->getDossierable();

		if($targetDossier = $dossierable->dossiers->where('form_id', $this->getTargetFormId())->first())
			return $targetDossier;

		return $this->createTargetDossierForDossierable($dossierable);
	}

	public function createTargetDossierForDossierable($dossierable) : Dossier
	{
		return DossierCreatorHelper::createByForm(
			$dossierable,
			$this->getTargetForm()
		);
	}

	public function createTargetDossierForDossierrow(Dossierrow $dossierrow) : Dossier
	{
		$dossierable = $dossierrow->getDossierable();

		return $this->createTargetDossierForDossierable($dossierable);
	}

	public function parseDossierrows()
	{
		foreach($this->getCleanedDossierrowsToConvert() as $dossierrow)
		{
			$targetDossier = $this->getTargetDossierByDossierrow($dossierrow);

			if($existingDossierrow = $targetDossier->dossierrows()->where('formrow_id', $this->targetRow->getKey())->first())
			{
				if($existingDossierrow->isPopulated())
				{
					if($this->targetRow->isRepeatable())
					{
						$dossierrow->dossier_id = $targetDossier->getKey();
						$dossierrow->formrow_id = $this->targetRow->getKey();
						$dossierrow->save();

						continue;
					}

					$targetDossier = $this->createTargetDossierForDossierrow($dossierrow);
					$existingDossierrow = $targetDossier->dossierrows()->where('formrow_id', $this->targetRow->getKey())->first();
				}

				$existingDossierrow->setValue($dossierrow->getValue());
				$existingDossierrow->save();
				$dossierrow->delete();
			}
			else
			{
				dd('manca una riga esistente?!');
			}
		}

		return true;
	}
}