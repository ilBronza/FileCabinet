<?php

namespace IlBronza\FileCabinet\Models\Traits;

trait DossierCheckersTrait
{
	public function isPopulated() : bool
	{
		return !! $this->getPopulatedAt();
	}

	public function mustBeUpdated() : bool
	{
		$formrowIds = $this->getFormrowsIds();

		$dossierrowIds = $this->getDossierrowsFormrowsIds();

		return count($formrowIds->diff($dossierrowIds));
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
