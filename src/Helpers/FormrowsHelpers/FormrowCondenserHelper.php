<?php

namespace IlBronza\FileCabinet\Helpers\FormrowsHelpers;

use IlBronza\FileCabinet\Helpers\DossierCreatorHelper;

class FormrowCondenserHelper extends FormrowCondenserBaseHelper
{
	static function condense(Formrow $formrow, Formrow $targetRow)
	{
		$helper = new static;

		$helper->setFormrow($formrow);
		$helper->setTargetRow($targetRow);

		if($formrow->getRowType()->getDatabaseField() != $targetRow->getRowType()->getDatabaseField())
			abort(403, "\"{$formrow->getName()}\" del form \"{$formrow->getForm()?->getName()}\" e \"{$targetRow->getName()}\" del form \"{$targetRow->getForm()?->getName()}\" non sono dello stesso tipo.<br />{$formrow->getName()} è \"{$formrow->getRowType()->getDatabaseField()}\" e {$targetRow->getName()} è \"{$targetRow->getRowType()->getDatabaseField()}\"");

		return $helper->parseDossierrows();
	}

	public function closeFormrow()
	{
		$formrow->delete();

		return true;
	}
}