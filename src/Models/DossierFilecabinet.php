<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Traits\Model\CRUDUseUlidKeyTrait;
use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use IlBronza\CRUD\Traits\Model\PackagedModelsTrait;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DossierFilecabinet extends Pivot
{
    use PackagedModelsTrait;
    use CRUDUseUuidTrait;

    static $packageConfigPrefix = 'filecabinet';
    public ? string $translationFolderPrefix = 'filecabinet';

	static $modelConfigPrefix = 'dossierFilecabinet';
	static $deletingRelationships = [];
}
