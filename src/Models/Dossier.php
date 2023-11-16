<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Form;

class Dossier extends BaseModel
{
	public $deletingRelationships = ['dossierrows'];

	public function form()
	{
		return $this->belongsTo(Form::class);
	}

	public function dossierrows()
	{
		return $this->hasMany(Dossierrow::class);
	}

}
