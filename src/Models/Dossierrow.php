<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Traits\Model\CRUDModelTrait;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Filecabinetrow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Dossierrow extends Model
{
	use SoftDeletes;
	use CRUDModelTrait;

	public $deletingRelationships = [];

	public function dossier()
	{
		return $this->belongsTo(Dossier::class);
	}
}
