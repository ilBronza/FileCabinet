<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\Buttons\Button;
use IlBronza\CRUD\Helpers\ModelManagers\Interfaces\ClonableModelInterface;
use IlBronza\CRUD\Helpers\ModelManagers\Traits\ClonableModelTrait;
use IlBronza\CRUD\Traits\CRUDSluggableTrait;
use IlBronza\CRUD\Traits\Model\CRUDParentingTrait;
use IlBronza\Category\Traits\InteractsWithCategoryTrait;
use IlBronza\FileCabinet\Models\Traits\FormCheckersTrait;
use IlBronza\FileCabinet\Models\Traits\FormGettersSettersTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use function app;
use function array_merge;
use function config;
use function get_class;
use function ini_set;
use function request;
use function route;

class Form extends BaseFileCabinetModel implements ClonableModelInterface
{
	use CRUDParentingTrait;
	use CRUDSluggableTrait;
	use FormGettersSettersTrait;
	use FormCheckersTrait;

	use InteractsWithCategoryTrait;


	/** START CLONE INTERFACE MANAGEMENT **/

	use ClonableModelTrait;

    public function getClonableRelations() : array
    {
        return [
            'formrows'
        ];
    }

    public function getNotClonableFields() : array
    {
        return [];
    }

	/** END CLONE INTERFACE MANAGEMENT **/



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

	public function getRelatedDossiers() : Collection
	{
		ini_set('max_execution_time', 60);
		ini_set('memory_limit', -1);

		return $this->dossiers()->with(
			'form',
			'dossierrows.formrow',
			'schedules',
			'dossierrows.schedules',
			'filecabinets',
			'dossierable'
		)->get();
	}

	public function formrows() : HasMany
	{
		return $this->hasMany(Formrow::getProjectClassName());
	}

	public function getFormrowsMaxSortingIndex() : int
	{
		return $this->formrows()->max('sorting_index') ?? 0;
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

	public function getDossiersIndexUrl() : string
	{
		return route(config('filecabinet.routePrefix') . 'dossiers.byForm', ['form' => $this]);
	}


	public function getDescription() : ? string
	{
		return $this->description;
	}

	public function hasBeenUsed() : bool
	{
		return $this->dossiers()->take(1)->count() > 0;
	}

	public function getShortName()
	{
		return Str::limit($this->getName(), 20);
	}

	public function getMoveUrl()
	{
		return $this->getKeyedRoute('move', [
			'formrow' => request()->formrow,
			'form' => $this->getKey()
		]);
	}
}
