<?php

namespace IlBronza\FileCabinet\Helpers\FormrowsHelpers;

use IlBronza\Datatables\Providers\FieldsGroupsMergerHelper;
use IlBronza\FileCabinet\Models\Form;

use function config;
use function dd;

class FormrowsDatatableFieldsGroupsHelper
{
	public Form $form;

	public function __construct(Form $form)
	{
		$this->setForm($form);
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
			$fields['mySelf' . $formrow->getSlug()] = [
				'type' => 'function',
				'translatedName' => $formrow->getName(),
				'function' => 'getValueByFormrow',
				'staticVariableValue' => $formrow
			];
		}

		return [
			'fields' => $fields
		];
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
}