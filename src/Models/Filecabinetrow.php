<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Traits\CRUDSluggableTrait;
use IlBronza\CRUD\Traits\Model\CRUDModelTrait;
use IlBronza\CRUD\Traits\Model\CRUDRelationshipModelTrait;
use IlBronza\Category\Models\Category;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Filecabinet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Filecabinetrow extends Model
{
	use SoftDeletes;

	use CRUDModelTrait;
	use CRUDRelationshipModelTrait;

	use CRUDSluggableTrait;

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

    public function getDeleteUrl(array $data = [])
    {
		return route('filecabinets.filecabinetrows.destroy', [
			$this->filecabinet_id,
			$this->getKey()
		]);
    }

    public function getEditUrl(array $data = [])
    {
		return route('filecabinets.filecabinetrows.edit', [
			$this->filecabinet_id,
			$this->getKey()
		]);
    }

    public function getShowUrl(array $data = [])
    {
        return route('filecabinets.filecabinetrows.show', [
        	$this->filecabinet_id,
        	$this->getKey()
        ]);
    }

	public function getSelectPossibleTypeValues()
	{
		$result = [];

		foreach(static::$possibleTypes as $type)
			$result[$type] = trans('filecabinet::filecabinetrows.type' . ucfirst($type));

		return $result;
	}
}
