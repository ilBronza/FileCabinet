<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Models\SluggableBaseModel;
use IlBronza\CRUD\Traits\Model\CRUDBelongsToModelRouteTrait;
use IlBronza\Category\Models\Category;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Filecabinet;

class Filecabinetrow extends SluggableBaseModel
{
	use CRUDBelongsToModelRouteTrait;

	public function getOwnerModelLocalKey() : string
	{
		return 'filecabinet_slug';
	}

	public function getOwnerModelClass() : string
	{
		return Filecabinet::class;
	}

	public $deletingRelationships = ['dossierrows'];

	public function dossierrows()
	{
		return $this->hasMany(Dossierrow::class);
	}

	public function filecabinet()
	{
		return $this->belongsTo(Filecabinet::class);
	}

	protected $dates = [
		'deleted_at'
	];

	static $possibleTypes = [
		'string',
		'text',
		'date',
		'datetime',
		'time',
		'color',
		'file',
		'integer',
		'float',
		'boolean',
		'enum',
		'set',
		'email'
	];

	public function getSelectPossibleTypeValues()
	{
		$result = [];

		foreach(static::$possibleTypes as $type)
			$result[$type] = trans('filecabinet::filecabinetrows.type' . ucfirst($type));

		return $result;
	}
}
