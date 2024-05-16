<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Traits\Model\CRUDUseUlidKeyTrait;
use IlBronza\FileCabinet\Models\Traits\DossierButtonsRoutesTrait;
use IlBronza\FileCabinet\Models\Traits\DossierCheckersTrait;
use IlBronza\FileCabinet\Models\Traits\DossierGettersSettersTrait;
use IlBronza\FileCabinet\Models\Traits\DossierHtmlFormTrait;
use IlBronza\FileCabinet\Models\Traits\DossierRelationsTrait;
use IlBronza\FileCabinet\Models\Traits\DossierRenderTrait;
use IlBronza\FileCabinet\Models\Traits\DossierScopesTrait;

class Dossier extends BaseFileCabinetModel
{
	use CRUDUseUlidKeyTrait;
	use DossierHtmlFormTrait;
	use DossierRenderTrait;
	use DossierRelationsTrait;
	use DossierGettersSettersTrait;
	use DossierButtonsRoutesTrait;
	use DossierCheckersTrait;
	use DossierScopesTrait;

	protected $dates = [
		'populated_at',
		'must_be_updated_at'
	];

	static $modelConfigPrefix = 'dossier';
	static $deletingRelationships = ['dossierrows'];

}
