<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossiers;

use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDPlainIndexTrait;
use IlBronza\FileCabinet\Http\Controllers\Dossiers\DossierCRUD;

use function ini_set;

class DossierIndexController extends DossierCRUD
{
	use CRUDPlainIndexTrait;
	use CRUDIndexTrait;

	public $allowedMethods = ['index'];

	public function getIndexFieldsArray()
	{
		//DossierFieldsGroupParametersFile
		return config('filecabinet.models.dossier.fieldsGroupsFiles.index')::getFieldsGroup();
	}

	public function getRelatedFieldsArray()
	{
		//DossierRelatedFieldsGroupParametersFile
		return config('filecabinet.models.dossier.fieldsGroupsFiles.related')::getFieldsGroup();
	}

	public function getIndexElements()
	{
		ini_set('max_execution_time', 60);
		ini_set('memory_limit', - 1);

		return $this->getModelClass()::with(
			'filecabinets',
			'form',
			'schedules',
			'dossierable',
			'dossierrows.formrow',
			'dossierrows.schedules',
			'schedules.type'
		)->get();
	}

}
