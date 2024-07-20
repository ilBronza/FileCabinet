<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\Category\Models\Category;
use IlBronza\Category\Traits\InteractsWithCategoryStandardMethodsTrait;
use IlBronza\Category\Traits\InteractsWithCategoryTrait;
use IlBronza\FileCabinet\Models\BaseFileCabinetModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilecabinetTemplate extends BaseFileCabinetModel
{
	use InteractsWithCategoryTrait;
	use InteractsWithCategoryStandardMethodsTrait;

	static $modelConfigPrefix = 'filecabinetTemplate';
	static $deletingRelationships = [];

	protected $casts = [
		'models' => 'json'
	];

	public function hasForcedPopulation() : bool
	{
		return true;
	}

	public function hasForcedConsecutiveness() : bool
	{
		return !! $this->force_consecutiveness;
	}

	public function getEditPdfTemplateUrl()
	{
		return $this->getKeyedRoute('managePdfTemplate');
	}
}
