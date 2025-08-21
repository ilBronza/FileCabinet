<?php

namespace IlBronza\FileCabinet\Http\Controllers\Formrows;

use IlBronza\CRUD\Traits\CRUDRelationshipTrait;
use IlBronza\CRUD\Traits\CRUDShowTrait;
use IlBronza\FileCabinet\Helpers\FormrowsHelpers\FormrowCondenserHelper;

use IlBronza\FileCabinet\Helpers\FormrowsHelpers\FormrowMoverHelper;
use IlBronza\FileCabinet\Models\Form;

use function app;
use function redirect;

class FormrowMoveController extends FormrowCRUD
{
	public $allowedMethods = ['move'];

	public function move(string $formrow, string $form)
	{
		$formrow = $this->getModelClass()::findOrFail($formrow);
		$form = Form::gpc()::findOrFail($form);

		FormrowMoverHelper::move($formrow, $form);

		return redirect()->to($returnUrl);
	}
}
