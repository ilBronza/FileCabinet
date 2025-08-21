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
					'formrows' => [
						'controller' => config('filecabinet.models.formrow.controllers.index'),
						//FormrowByFormFieldsGroupParametersFile
						'fieldsGroupsParametersFile' => config('filecabinet.models.formrow.fieldsGroupsFiles.byForm'),
						'sorting' => true,
						'buttons' => [
							$this->getModel()->getCreateFormrowButton()
						]
					],
//					'dossiers' => [
//						//DossierIndexController
//						'controller' =>  config('filecabinet.models.dossier.controllers.index'),
//						'elementGetterMethod' => 'getRelatedDossiers'
//					],
				]
			]
		];
	}
}