<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Traits\CRUDSluggableTrait;
use IlBronza\CRUD\Traits\Model\CRUDModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Filecabinetrow extends Model
{
	use SoftDeletes;
	use CRUDModelTrait;
	use CRUDSluggableTrait;

	protected $dates = [
		'deleted_at'
	];

	public $deletingRelationships = [];

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
