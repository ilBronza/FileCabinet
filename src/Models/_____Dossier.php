<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Traits\Model\CRUDModelTrait;
use IlBronza\CRUD\Traits\Model\CRUDRelationshipModelTrait;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Filecabinet;
use IlBronza\FileCabinet\Models\Filecabinetrow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dossier extends Model
{
	use SoftDeletes;

	use CRUDModelTrait;
	use CRUDRelationshipModelTrait;

	public $deletingRelationships = ['dossierrows'];

	public function filecabinet()
	{
		return $this->belongsTo(Filecabinet::class);
	}

	public function dossierrows()
	{
		return $this->hasMany(Dossierrow::class);
	}

}
