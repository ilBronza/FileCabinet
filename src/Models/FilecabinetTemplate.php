<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\Category\Models\Category;
use IlBronza\Category\Traits\InteractsWithCategoryStandardMethodsTrait;
use IlBronza\Category\Traits\InteractsWithCategoryTrait;
use IlBronza\CRUD\Traits\Media\InteractsWithMedia;
use IlBronza\FileCabinet\Models\BaseFileCabinetModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FilecabinetTemplate extends BaseFileCabinetModel implements HasMedia
{
	use InteractsWithMedia;
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

	public function getPdfTitle() : ? string
	{
		return $this->pdf_title ?? $this->getName();
	}

	public function hasForcedConsecutiveness() : bool
	{
		return !! $this->force_consecutiveness;
	}

	public function getEditPdfTemplateUrl()
	{
		return $this->getKeyedRoute('managePdfTemplate');
	}

	public function mustPrintMenu() : bool
	{
		return !! $this->pdf_print_menu;
	}

	public function getPdfImage() : ? Media
	{
		return $this->getMedia('pdf_image')->first();
	}
}
