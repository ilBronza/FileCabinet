<?php

namespace IlBronza\FileCabinet\Models;


use IlBronza\CRUD\Traits\CRUDSluggableTrait;
use IlBronza\CRUD\Traits\Model\CRUDModelTrait;
use IlBronza\FileCabinet\Models\Filecabinetrow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filecabinet extends Model
{
	use HasFactory;

	use CRUDModelTrait;
	use CRUDSluggableTrait;

	public function filecabinetrows()
	{
		return $this->hasMany(Filecabinetrow::class);
	}
}
