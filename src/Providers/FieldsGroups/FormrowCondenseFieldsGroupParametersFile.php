<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class FormrowCondenseFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
			'translationPrefix' => 'filecabinet::fields',
			'fields' => [
				'mySelfPrimary' => 'primary',
				'mySelfCondense' => [
					'type' => 'links.link',
					'confirmMessage' => 'Sei sicuro di voler condensare questo oggetto?',
					'faIcon' => 'arrows-to-dot',
					'function' => 'getCondenseUrl'
				],
				'name' => 'flat',
				'slug' => 'flat',
				'description' => 'flat',
				'form' => 'relations.belongsTo',
				'type' => 'filecabinet::rowType',
			]
		];
	}
}