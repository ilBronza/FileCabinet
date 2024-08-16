<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossiers;

use IlBronza\Category\Models\Category;
use IlBronza\CRUD\Helpers\ModelHelpers\ModelFinderHelper;
use IlBronza\FileCabinet\Helpers\DossierCreatorHelper;
use IlBronza\FileCabinet\Models\Form;
use IlBronza\FormField\FormField;
use Illuminate\Database\Eloquent\Relations\Relation;

class DossierByModelCategoryController extends DossierCRUD
{
    public $allowedMethods = ['byModelCategory'];

	public function byModelCategory(string $model, string $key, string $category)
	{
		$model = ModelFinderHelper::getByClassKey($model, $key);

		$documentsCategory = ModelFinderHelper::getByClassSlug('Category', $category);

		$forms = Form::getByDirectCategory($documentsCategory);
		$documentsDossiers = $model->getDossiersByForms($forms);

		$formFields = [];

		foreach ($forms as $form)
		{
			if (! $documentDossier = $documentsDossiers->firstWhere('form_id', $form->id))
				if(! $documentDossier = DossierCreatorHelper::automaticallyCreateByForm($model, $form))
					continue;

			$formFields[$form->getSlug()] = FormField::createFromArray([
				'showLabel' => false,
				'name' => $form->getShortName(),
				'type' => 'filecabinet::providers.formFields.dossierStatus',
				'dossier' => $documentDossier,
				'rules' => [],
				'formSlug' => $form->getSlug(),
			]);
		}

		return view('filecabinet::dossiers._byModelCategoryFormFieldsTable', compact("formFields"));
	}
}
