<?php

namespace IlBronza\FileCabinet\Http\Controllers\Formrows;

use IlBronza\FileCabinet\Helpers\FormrowsHelpers\FormrowCondenserHelper;

use function dd;
use function get_class_methods;

class FormrowCondenseController extends FormrowCRUD
{
	public $allowedMethods = ['condense'];

	public function condense(string $formrow, string $targetRow)
	{
		$formrow = $this->getModelClass()::findOrFail($formrow);
		$targetRow = $this->getModelClass()::with('form')->findOrFail($targetRow);

		$returnUrl = $formrow->getForm()->getShowUrl();

		FormrowCondenserHelper::condense($formrow, $targetRow);

		if($returnUrl)
			return redirect()->to($returnUrl);

		return app('filecabinet')->route('forms.index');
	}
}
