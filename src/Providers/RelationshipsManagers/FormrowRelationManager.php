<?php

namespace IlBronza\FileCabinet\Providers\RelationshipsManagers;

use IlBronza\CRUD\Providers\RelationshipsManager\RelationshipsManager;

class FormrowRelationManager extends RelationshipsManager
{
	public function getAllRelationsParameters() : array
	{
		return [
			'show' => [
				'relations' => [
					'dossierrows' => config('filecabinet.models.dossierrow.controllers.index'),
					'form' => config('filecabinet.models.form.controllers.show'),
				]
			]
		];
	}
}