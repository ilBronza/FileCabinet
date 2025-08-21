<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossiers;

use IlBronza\CRUD\Helpers\ModelHelpers\ModelFinderHelper;
use IlBronza\FileCabinet\Helpers\DossierCreatorHelper;
use IlBronza\FileCabinet\Models\Form;
use IlBronza\FormField\FormField;

use function compact;
use function view;

class DossierByModelFormController extends DossierCRUD
{
    public $allowedMethods = ['byModelForm'];
	public function byModelForm(string $model, string $key, string $form)
	{
		$model = ModelFinderHelper::getByClassKey($model, $key);

		$form = Form::where('slug', $form)->firstOrFail();

		$dossier = $model->getValidDossierByForm($form);

		if(! $dossier)
			$dossier = DossierCreatorHelper::getOrCreateByForm($model, $form);

		return $dossier->render();

//		return view('filecabinet::dossiers._byModelCategoryFormFieldsTable', compact("formFields"));
	}
}
