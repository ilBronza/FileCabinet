<?php

namespace IlBronza\FileCabinet\Providers\RelationshipsManagers;

use IlBronza\CRUD\Providers\RelationshipsManager;

class FilecabinetRelationManager Extends RelationshipsManager
{
	public function getAllRelationsParameters()
	{
		return [
			'show' => [
				'relations' => [
					'filecabinetrows' => CrudFilecabinetrowController::class,
				]
			]
		];
	}
}