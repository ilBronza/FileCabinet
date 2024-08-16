<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossiers;

use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\FileCabinet\Helpers\FormrowsHelpers\FormrowsDatatableFieldsGroupsHelper;
use IlBronza\FileCabinet\Models\Form;
use Illuminate\Http\Request;

use function config;

class DossierByFormIndexController extends DossierCRUD
{
	use CRUDIndexTrait;

	public $allowedMethods = ['index'];

	public function getIndexFieldsArray()
    {
		return FormrowsDatatableFieldsGroupsHelper::getDossierFieldsGroupsByFormAndParametersFileName(
			$this->form,
			config('filecabinet.models.dossier.fieldsGroupsFiles.index')
		);
	}

	public function index(Request $request, string $form)
	{
		$this->form = Form::getProjectClassname()::find($form);

		return $this->_index($request);
	}

    public function getIndexElements()
    {
        return $this->getModelClass()::byForm($this->form)
				->with(
                    'filecabinets',
                    'dossierable',
					'dossierrows.formrow',
					'dossierrows.schedules'
                )
                ->get();
    }

}
