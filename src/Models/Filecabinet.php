<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Models\SluggableBaseModel;
use IlBronza\CRUD\Traits\CRUDSluggableTrait;
use IlBronza\Category\Traits\InteractsWithCategoryTrait;

class Filecabinet extends SluggableBaseModel
{
	use InteractsWithCategoryTrait;

	public $deletingRelationships = ['dossiers', 'filecabinetrows'];

	public function getCategoryModel() : string
	{
		return config('filecabinet.categories.model');
	}

	public function getCategoriesCollection() : ? string
	{
		return config('filecabinet.categories.collection');
	}

	public function dossiers()
	{
		return $this->hasMany(Dossier::class);
	}

	public function filecabinetrows()
	{
		return $this->hasMany(Filecabinetrow::class);
	}
}
