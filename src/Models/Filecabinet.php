<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\Buttons\Icons\FaIcon;
use IlBronza\CRUD\Interfaces\RecursiveTreeInterface;
use IlBronza\CRUD\Traits\IlBronzaPackages\CRUDExtraButtonsTrait;
use IlBronza\CRUD\Traits\Model\CRUDParentingTrait;
use IlBronza\CRUD\Traits\Model\CRUDSortingIndexTrait;
use IlBronza\FileCabinet\Models\BaseFileCabinetModel;
use IlBronza\FileCabinet\Models\Traits\FilecabinetButtonsRoutesTrait;
use IlBronza\FileCabinet\Models\Traits\FilecabinetCompletionTrait;
use IlBronza\FileCabinet\Models\Traits\FilecabinetRelationsTrait;
use IlBronza\FileCabinet\Models\Traits\FilecabinetScopesTrait;
use IlBronza\Menu\Interfaces\NavbarableElementInterface;
use Illuminate\Support\Collection;

class Filecabinet extends BaseFileCabinetModel implements 
	RecursiveTreeInterface,
	NavbarableElementInterface
{
    use CRUDParentingTrait;

    use CRUDSortingIndexTrait;

    use FilecabinetRelationsTrait;
    use FilecabinetButtonsRoutesTrait;
    use FilecabinetScopesTrait;
    use FilecabinetCompletionTrait;

    use CRUDExtraButtonsTrait;

    public function getContentElements() : Collection
    {
    	return $this->getDossiers()->sortBy('sorting_index');
    }

    static $parentKeyName = 'parent_id';

	static $modelConfigPrefix = 'filecabinet';
	static $deletingRelationships = ['children', 'dossiers'];

	public bool $showChildrenContent = false;

	public function hasDossiers() : bool
	{
		if($this->relationLoaded('dossiers'))
			return $this->dossiers->count() > 0;

		return $this->dossiers()->count() > 0;
	}

	public function getPopulateUrl() : string
	{
		return $this->getKeyedRoute('populate');
	}

	static function findWithTree(string $filecabinet)
	{
		return static::getTreeWithRelatedElements($filecabinet, []);
	}

	public function getName() : ? string
	{
		return $this->getCategory()->getName();
	}

	public function getId() : ? string
	{
		return $this->getKey();
	}

	public function getButtonText() : string
	{
		return $this->getName();
	}

	public function getButtonUrl() : string
	{
		return $this->getPopulateUrl();
	}

	public function getIcon() : ? string
	{
		return null;
	}

	public function buildButtonBadgeHtml(int $toPopulate, int $populated)
	{
		return "<span class='uk-text-normal'>{$toPopulate}</span>/<strong>{$populated}</strong>";
	}

	public function getButtonBadgeText() : ? string
	{
		if(! $this->relationloaded('dossiers'))
			return $this->buildButtonBadgeHtml(
				$this->dossiers()->populated()->count(),
				$this->dossiers()->count()
			);

		return $this->buildButtonBadgeHtml(
				$this->dossiers->filter(function($item)
					{
						return $item->isPopulated();
					})->count(),
				$this->dossiers->count()
			);
	}

	public function mustShowChildrenContent() : bool
	{
		return $this->showChildrenContent;
	}

	public function getCreatedAt()
	{
		return $this->created_at;
	}

	public function getStatusString()
	{
		$iconMethod = $this->isPopulated() ? 'check' : 'xmark';

		$iconString = FaIcon::$iconMethod();

		return "{$iconString} {$this->getName()}";
	}

	public function getPdfTitle()
	{
		if($title = $this->getCategory()->getPdfTitle())
			return $title;

		return $this->getName();
	}

	public function canGeneratePartialPdf() : bool
	{
		return config('filecabinet.filecabinet.buttons.showPrintPartialPdf', true);
	}
}
