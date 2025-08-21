<?php

namespace IlBronza\FileCabinet\Helpers\FormrowsHelpers;

use IlBronza\FileCabinet\Helpers\DossierCreatorHelper;

use IlBronza\FileCabinet\Models\Form;
use IlBronza\FileCabinet\Models\Formrow;

class FormrowMoverHelper extends FormrowCondenserBaseHelper
{
	static function move(Formrow $formrow, Form $form)
	{
		$helper = new static;

		$helper->setFormrow($formrow);
		$helper->setTargetForm($form);

		if($formrow->getForm()?->is($form))
			abort(403, "Il formrow \"{$formrow->getName()}\" è già nel form \"{$form->getName()}\"");

		return $helper->parseDossierrows();
	}

	public function closeFormrow()
	{
		$formrow->form_id = $form->getKey();
		$formrow->save();

		return true;
	}
}