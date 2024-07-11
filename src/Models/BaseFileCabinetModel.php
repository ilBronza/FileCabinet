<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Models\PackagedBaseModel;
use IlBronza\CRUD\Traits\Model\CRUDUseUlidKeyTrait;

class BaseFileCabinetModel extends PackagedBaseModel
{
	use CRUDUseUlidKeyTrait;

    public $incrementing = false;
    protected $keyType = 'string';

	static $packageConfigPrefix = 'filecabinet';
}