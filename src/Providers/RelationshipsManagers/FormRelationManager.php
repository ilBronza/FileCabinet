<?php

namespace IlBronza\FileCabinet\Providers\RelationshipsManagers;

use IlBronza\CRUD\Providers\RelationshipsManager\RelationshipsManager;

use function config;

class FormRelationManager Extends RelationshipsManager
{
	public  function getAllRelationsParameters() : array
	{
		return [
			'show' => [
				'relations' => [
					'formrows' => config('filecabinet.models.formrow.controllers.index'),
					'dossiers' => [
						//DossierIndexController
						'controller' =>  config('filecabinet.models.dossier.controllers.index'),
						'elementGetterMethod' => 'getRelatedDossiers'
					],
				]
			]
		];
	}
}