<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Models\BaseModel;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Factories\FormFactory;
use IlBronza\FileCabinet\Models\Formrow;
use Illuminate\Database\Eloquent\Factories\Factory;

class Form extends BaseModel
{
	public $deletingRelationships = ['dossiers', 'formrows'];

	protected static function newFactory(): Factory
	{
		return FormFactory::new();
	}

	public function dossiers()
	{
		return $this->hasMany(Dossier::class);
	}

	public function filecabinetrows()
	{
		return $this->hasMany(Formrow::class);
	}
}
