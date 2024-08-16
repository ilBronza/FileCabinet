<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Traits\Model\CRUDParentingTrait;
use IlBronza\FileCabinet\Helpers\DossierStatusHelper;
use IlBronza\FileCabinet\Models\Traits\DossierButtonsRoutesTrait;
use IlBronza\FileCabinet\Models\Traits\DossierCheckersTrait;
use IlBronza\FileCabinet\Models\Traits\DossierGettersSettersTrait;
use IlBronza\FileCabinet\Models\Traits\DossierHtmlFormTrait;
use IlBronza\FileCabinet\Models\Traits\DossierRelationsTrait;
use IlBronza\FileCabinet\Models\Traits\DossierRenderTrait;
use IlBronza\FileCabinet\Models\Traits\DossierScopesTrait;
use IlBronza\Menu\Interfaces\NavbarableElementInterface;
use IlBronza\Schedules\Traits\InteractsWithSchedule;
use Illuminate\Support\Collection;

use function _;
use function dd;
use function get_class_methods;
use function json_encode;

class Dossier extends BaseFileCabinetModel implements NavbarableElementInterface
{
	use InteractsWithSchedule;
	use CRUDParentingTrait;

	use DossierHtmlFormTrait;
	use DossierRenderTrait;
	use DossierRelationsTrait;
	use DossierGettersSettersTrait;
	use DossierButtonsRoutesTrait;
	use DossierCheckersTrait;
	use DossierScopesTrait;

	protected $casts = [
		'populated_at' => 'date',
		'must_be_updated_at' => 'date'
	];

	static $modelConfigPrefix = 'dossier';
	static $deletingRelationships = ['dossierrows'];

	public function getButtonUrl() : string
	{
		return '#dossier' . $this->getKey();
	}

	public function getButtonText() : string
	{
		return $this->getName();
	}

	public function getIcon() : ? string
	{
		return null;
	}

	public function getButtonBadgeText() : ? string
	{
		return null;
	}

	public function getContentElements() : Collection
	{
		return $this->getChildren()->sortBy('sorting_index');
	}

	public function getAllSchedulesAttribute() : Collection
	{
		$dossierSchedules = $this->getSchedules();

		$dossierrowSchedules = $this->getDossierrowsSchedules();

		return $dossierrowSchedules->merge($dossierSchedules);
	}

	public function getAllSchedules()
	{
		return $this->all_schedules;
	}

	public function getStatus()
	{
		return DossierStatusHelper::getStatus($this);
	}

	public static function boot() {
		parent::boot();

		//once created/inserted successfully this method fired, so I tested foo
		static::saving(function (self $model)
		{
			$model->checkForCompletion(false);
		});
	}

}
