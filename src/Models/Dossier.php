<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Traits\Model\CRUDModelTrait;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Filecabinetrow;
use Illuminate\Database\Eloquent\Model;

class Dossier extends Model
{
	use CRUDModelTrait;

	public function dossierrows()
	{
		return $this->hasMany(Dossierrow::class);
	}

}
