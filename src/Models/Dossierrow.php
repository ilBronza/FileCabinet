<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Traits\Model\CRUDModelTrait;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Filecabinetrow;
use Illuminate\Database\Eloquent\Model;


class Dossierrow extends Model
{
	use CRUDModelTrait;

	public function dossier()
	{
		return $this->belongsTo(Dossier::class);
	}

}
