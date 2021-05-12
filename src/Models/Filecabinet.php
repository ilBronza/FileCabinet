<?php

namespace IlBronza\FileCabinet\Models;


use IlBronza\CRUD\Traits\CRUDSluggableTrait;
use IlBronza\CRUD\Traits\Model\CRUDModelTrait;
use IlBronza\CRUD\Traits\Model\CRUDRelationshipModelTrait;
use IlBronza\Category\Models\Category;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Filecabinetrow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Filecabinet extends Model
{
	use HasFactory;

	use SoftDeletes;

	use CRUDModelTrait;
	use CRUDRelationshipModelTrait;

	use CRUDSluggableTrait;

	public $deletingRelationships = ['dossiers', 'filecabinetrows'];

	public function categories()
	{
		return $this->morphToMany(Category::class, 'categorizeable');
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
