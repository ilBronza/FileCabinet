<?php

namespace IlBronza\FileCabinet\Providers\RelationshipsManagers;

use IlBronza\CRUD\Providers\RelationshipsManager\RelationshipsManager;

class FilecabinetTemplateRelationManager extends RelationshipsManager
{
	public function getAllRelationsParameters() : array
	{
		return [
			'show' => [
				'relations' => [
					'formrow' => config('filecabinet.models.formrow.controllers.show'),
					'dossier' => config('filecabinet.models.dossier.controllers.show'),
				]
			]
		];
	}
}