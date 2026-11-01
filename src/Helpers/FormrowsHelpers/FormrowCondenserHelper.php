<?php

namespace IlBronza\FileCabinet\Helpers\FormrowsHelpers;

use IlBronza\FileCabinet\Models\Formrow;

class FormrowCondenserHelper extends FormrowCondenserBaseHelper
{
	static function condense(Formrow $formrow, Formrow $targetRow)
	{
		abort_unless($formrow->getRowType()->isAvailable() && $targetRow->getRowType()->isAvailable(), 422,
			'Tipi di campo non disponibili: impossibile convertire i valori salvati.');

		$helper = new static;

		$helper->setFormrow($formrow);
		$helper->setTargetRow($targetRow);

		if($formrow->getRowType()->getDatabaseField() != $targetRow->getRowType()->getDatabaseField())
			abort(403, "\"{$formrow->getName()}\" del form \"{$formrow->getForm()?->getName()}\" e \"{$targetRow->getName()}\" del form \"{$targetRow->getForm()?->getName()}\" non sono dello stesso tipo.<br />{$formrow->getName()} è \"{$formrow->getRowType()->getDatabaseField()}\" e {$targetRow->getName()} è \"{$targetRow->getRowType()->getDatabaseField()}\"");

		$helper->parseDossierrows();

		$formrow->delete();

		return ;
	}

	public function closeFormrow()
	{
		$formrow->delete();

		return true;
	}
}
