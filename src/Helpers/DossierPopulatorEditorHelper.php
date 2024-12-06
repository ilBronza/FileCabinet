<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Dossierrow;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

use function array_pop;
use function count;
use function dd;

class DossierPopulatorEditorHelper extends DossierPopulatorHelper
{
	public function getDossierValidationRules() : array
	{
		$dossierrowId = $this->getDossierrowId();

		$result = $this->getDossier()->getDossierValidationRuleByDossierrowId($dossierrowId);

		return [
			'value' => array_pop($result)
		];
	}

	public function populate()
	{
		//		$this->dossierrow = $this->getDossier()->getDossierrowByFormrowSlug(
		$this->dossierrow = $this->getDossier()->getDossierrowBydossierrowId(
			$this->getDossierrowId()
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
		$updateParameters[$this->getDossierrowId()] = $this->getRequest()->value;
		$updateParameters['update-editor'] = true;
		$updateParameters['field'] = $this->getDossierrowId();
		$updateParameters['model-id'] = $this->dossierrow->getKey();
		$updateParameters['value'] = $this->getRequest()->value;

		$updateParameters['message'] = trans('filecabinet::messages.fieldUpdatedSuccessfully', ['field' => $this->dossierrow->getName()]);

		return $updateParameters;
	}

	public function getDossierrowId() : string
	{
		return $this->getRequest()->input('field');
	}

}