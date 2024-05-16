<?php

namespace IlBronza\FileCabinet\Models\Traits;

use IlBronza\FileCabinet\Helpers\DossierrowFormFieldHelper;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\DossierTempFieldsetsParameters;
use IlBronza\FormField\Helpers\FormFieldsProvider\FormFieldsProvider;
use IlBronza\FormField\Helpers\FormFieldsProvider\FormfieldParametersHelper;
use IlBronza\Form\Form as IbForm;
use Illuminate\Support\Collection;

trait DossierHtmlFormTrait
{
	public function getDossierValidationRules() : array
	{
		$result = [];

		foreach($this->getDossierrows() as $dossierrow)
		{
			$fieldname = $dossierrow->getFormfieldName();
			$rules = FormfieldParametersHelper::getValidationRulesFromModel($dossierrow);

			$result[$fieldname] = $rules;
		}

		return $result;
	}

	private function getFormFields() : Collection
	{
		$result = collect();

		foreach($this->getSortedDossierrows() as $dossierrow)
		{
			$formField = DossierrowFormFieldHelper::createFieldFromDossierrow($dossierrow);

			$result->push($formField);
		}

		return $result;
	}

	private function buildIbForm() : IbForm
	{
		$ibForm = new IbForm;

		$ibForm->setModel($this);

		$ibForm->setTitle($this->getName());

		if($description = $this->getDescription())
			$ibForm->setIntro($description);

		$ibForm->setCard();

		if($this->mustBeUpdated())
			$ibForm->addNavbarButton(
				$this->getUpdateFieldsButton()
			);

		if($this->isRepeatable())
			$ibForm->addNavbarButton(
				$this->getCreateNewInstanceButton()
			);

		$ibForm->setAction($this->getUpdateUrl());
		$ibForm->setMethod('PUT');

		$ibFormFields = $this->getFormFields();

		foreach($ibFormFields as $ibFormField)
			$ibForm->addFormField($ibFormField);

		return $ibForm;
	}

	public function getIbForm() : IbForm
	{
		if($this->ibForm)
			return $this->ibForm;

		$this->ibForm = $this->buildIbForm();

		return $this->ibForm;
	}

}
