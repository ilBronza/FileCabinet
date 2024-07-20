<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Traits\Model\CRUDParentingTrait;
use IlBronza\FileCabinet\Models\Traits\DossierButtonsRoutesTrait;
use IlBronza\FileCabinet\Models\Traits\DossierCheckersTrait;
use IlBronza\FileCabinet\Models\Traits\DossierGettersSettersTrait;
use IlBronza\FileCabinet\Models\Traits\DossierHtmlFormTrait;
use IlBronza\FileCabinet\Models\Traits\DossierRelationsTrait;
use IlBronza\FileCabinet\Models\Traits\DossierRenderTrait;
use IlBronza\FileCabinet\Models\Traits\DossierScopesTrait;
use IlBronza\Menu\Interfaces\NavbarableElementInterface;
use Illuminate\Support\Collection;

class Dossier extends BaseFileCabinetModel implements NavbarableElementInterface
{
	use CRUDParentingTrait;

	use DossierHtmlFormTrait;
	use DossierRenderTrait;
	use DossierRelationsTrait;
	use DossierGettersSettersTrait;
	use DossierButtonsRoutesTrait;
	use DossierCheckersTrait;
	use DossierScopesTrait;

	protected $dates = [
		'populated_at',
		'must_be_updated_at'
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
}
