<?php

namespace IlBronza\FileCabinet\Helpers\FormrowsHelpers;

use IlBronza\Datatables\Providers\FieldsGroupsMergerHelper;
use IlBronza\FileCabinet\Models\Form;

class FormrowsDatatableFieldsGroupsHelper
{
	public Form $form;

	public function __construct(Form $form)
	{
		$this->setForm($form);
	}

	static function getDossierFieldsGroupsByFormAndParametersFileName(Form $form, string $parametersFileName)
	{
		$helper = new FieldsGroupsMergerHelper();

		$helper->addFieldsGroupParameters($parametersFileName::getFieldsGroup());

		$formParameters = (new static($form))->getFieldsGroup();

		$helper->addFieldsGroupParameters(
			$formParameters
		);

		$helper->moveFieldToEnd('mySelfDelete');

		return $helper->getMergedFieldsGroups();
	}

	public function getForm() : Form
	{
		return $this->form;
	}

	public function setForm(Form $form) : void
	{
		$this->form = $form;
	}

	public function getFieldsGroup() : array
	{
		$fields = [];

		foreach ($this->getForm()->getFormrows() as $formrow)
		{
			if(! $formrow->canBeViewedInTable())
				continue;

			// if(! $formrow->public_frontpage)
			// 	continue;

			$fields['mySelf' . $formrow->getSlug()] = [
				'type' => $formrow->getDatatableFieldTypeString(),
				'overridingValueMethod' => 'getValueByFormrow',
//				'renderAs' => $formrow->getDatatableFieldTypeString(),
				'translatedName' => $formrow->getName(),
//				'function' => 'getValueByFormrow',
				'staticVariableValue' => $formrow,
			];
		}

		return [
			'fields' => $fields
		];
	}
}