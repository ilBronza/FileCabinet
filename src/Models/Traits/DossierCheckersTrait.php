<?php

namespace IlBronza\FileCabinet\Models\Traits;

trait DossierCheckersTrait
{
	public function isPopulated() : bool
	{
		return !! $this->getPopulatedAt();
	}

	public function checkForCompletion(bool $save = true) : bool
	{
		if(count($dossierrows = $this->getDossierrows()) == 0)
		{
			$this->setNotPopulated(false);

			return false;
		}
		
		$completed = true;

		foreach ($dossierrows as $dossierrow)
			if(! $dossierrow->isCompleted())
				$completed = false;

		if($completed)
			$this->setPopulated($save);

		return $completed;
	}

	public function mustBeUpdated() : bool
	{
		$formrowIds = $this->getFormrowsIds()->unique()->values();

		$dossierFormrowIds = $this->getDossierrowsFormrowsIds()->unique()->values();

		return $formrowIds->diff($dossierFormrowIds)->isNotEmpty()
			|| $dossierFormrowIds->diff($formrowIds)->isNotEmpty();
	}

	public function isRepeatable() : bool
	{
		return $this->getForm()?->isRepeatable();
	}

	public function hasPdfRenderTemplate() : bool
	{
		return false;
	}
}
