<?php

namespace IlBronza\FileCabinet\Models\Traits;

use IlBronza\FileCabinet\Helpers\DossierrowFormFieldHelper;
use IlBronza\FileCabinet\Providers\FieldsetsParameters\DossierTempFieldsetsParameters;
use IlBronza\FormField\Helpers\FormFieldsProvider\FormFieldsProvider;
use IlBronza\FormField\Helpers\FormFieldsProvider\FormfieldParametersHelper;
use IlBronza\Form\Form as IbForm;
use Illuminate\Support\Collection;

use function count;
use function dd;
use function get_class_methods;

trait DossierHtmlFormTrait
{
	public function getDossierValidationRuleByFormrowSlug(string $formrowSlug)
	{
		return $this->getRulesByDossierrows(
			collect([
				$this->getDossierrowByFormrowSlug($formrowSlug)
			])
		);
	}
	public function getDossierValidationRules() : array
	{
		return $this->getRulesByDossierrows(
			$this->getDossierrows()
		);
	}

	private function getFormFields() : Collection
	{
		$result = collect();

		$previousFormrowId = null;
		$repeating = false;

		foreach($this->getSortedDossierrows() as $dossierrow)
		{
			if(($repeating)&&($previousFormrowId != $dossierrow->getFormrowId()))
				$formField->setLastOfType();

			$formField = DossierrowFormFieldHelper::createFieldFromDossierrow($dossierrow);

			$repeating = ($previousFormrowId == $dossierrow->getFormrowId());
			$previousFormrowId = $dossierrow->getFormrowId();

			$result->push($formField);
		}

		if($repeating)
			$formField->setLastOfType();

		return $result;
	}

	private function buildIbForm() : IbForm
	{
		$ibForm = new IbForm;

		$ibForm->setModel($this);

		$ibForm->setTitle($this->getName());

		$ibForm->setUpdateEditor($this->hasUpdateEditor());

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

		$ibForm->hasShowLink(false);

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

	/**
	 * @return array
	 */
	public function getRulesByDossierrows(Collection $dossierrows) : array
	{
		$result = [];

		foreach ($dossierrows as $dossierrow)
		{
			$fieldname = $dossierrow->getFormfieldName();

			$rules = FormfieldParametersHelper::getValidationRulesFromModel($dossierrow);

			if ($dossierrow->getFormfieldType() == 'file')
				if (count($dossierrow->getMedia("*")) > 0)
					$rules = 'nullable';

			$result[$fieldname] = $rules;
		}

		return $result;
	}

}
