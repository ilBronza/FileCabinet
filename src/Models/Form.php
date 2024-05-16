<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\Buttons\Button;
use IlBronza\CRUD\Traits\CRUDSluggableTrait;

use IlBronza\CRUD\Traits\Model\CRUDUseUlidKeyTrait;
use IlBronza\Category\Traits\InteractsWithCategoryTrait;
use IlBronza\FileCabinet\Models\BaseFileCabinetModel;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\FileCabinet\Models\Traits\FormGettersSettersTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Form extends BaseFileCabinetModel
{
	use CRUDUseUlidKeyTrait;
	use CRUDSluggableTrait;
	use FormGettersSettersTrait;

	use InteractsWithCategoryTrait;

	static $deletingRelationships = ['formrows', 'dossiers'];

	public function getCategoriesCollection() : ? string
	{
		return null;
	}

	public function getCategoryModel() : string
	{
		return config('category.models.category.class');
	}

	static $modelConfigPrefix = 'form';

	public function dossiers() : HasMany
	{
		return $this->hasMany(Dossier::getProjectClassName());
	}

	public function formrows() : HasMany
	{
		return $this->hasMany(Formrow::getProjectClassName());
	}

	public function getFormrows() : Collection
	{
		return $this->formrows;
	}

	public function getCreateFormrowButton() : Button
	{
		return Button::create([
			'href' => $this->getCreateFormrowUrl(),
			'text' => 'filecabinet::formrows.create',
			'icon' => 'plus'
		]);
	}


	public function isRepeatable() :bool
	{
		return !! $this->repeatable;
	}

	public function getCreateFormrowUrl()
	{
		return route(config('filecabinet.routePrefix') . 'forms.formrows.create', ['form' => $this]);
	}

	public function getDescription() : ? string
	{
		return $this->description;
	}

	public function hasBeenUsed() : bool
	{
		return $this->dossiers()->take(1)->count() > 0;
	}
}
