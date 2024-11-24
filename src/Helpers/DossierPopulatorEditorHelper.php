<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Dossierrow;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

use function array_pop;
use function count;

class DossierPopulatorEditorHelper extends DossierPopulatorHelper
{
	public function getDossierValidationRules() : array
	{
		$formrowSlug = $this->getFormrowSlug();

		$result = $this->getDossier()->getDossierValidationRuleByFormrowSlug($formrowSlug);

		return [
			'value' => array_pop($result)
		];
	}

	public function populate()
	{
		$this->dossierrow = $this->getDossier()->getDossierrowByFormrowSlug(
			$this->getFormrowSlug()
		);

		$this->dossierrow->storeRowValue(
			$this->getParameterByName(
				'value'
			)
		);

		$this->getDossier()->touch();
	}

	public function returnResponse()
	{
		$updateParameters = [];

		$updateParameters['success'] = true;
		$updateParameters[$this->getFormrowSlug()] = $this->getRequest()->value;
		$updateParameters['update-editor'] = true;
		$updateParameters['field'] = $this->getFormrowSlug();
		$updateParameters['model-id'] = $this->dossierrow->getKey();
		$updateParameters['value'] = $this->getRequest()->value;

		return $updateParameters;
	}

	public function getFormrowSlug() : string
	{
		return $this->getRequest()->input('field');
	}

}