<?php

namespace IlBronza\FileCabinet\Providers\RelationshipsManagers;

use IlBronza\CRUD\Providers\RelationshipsManager\RelationshipsManager;

class FormRelationManager Extends RelationshipsManager
{
	public  function getAllRelationsParameters() : array
	{
		return [
			'show' => [
				'relations' => [
					'formrows' => config('filecabinet.models.formrow.controllers.index'),
					'dossiers' => [
						'controller' =>  config('filecabinet.models.dossier.controllers.index'),
						'elementGetterMethod' => 'getRelatedDossiers'
					],
				]
			]
		];
	}
}