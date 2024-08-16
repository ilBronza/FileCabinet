<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class DossierFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
            'translationPrefix' => 'filecabinet::fields',
            'fields' => 
            [
                'mySelfPrimary' => 'primary',
                'mySelfPopulate' => [
 					'type' => 'links.edit',
					'method' => 'getPopulateUrl'
				],
                'mySelfSee' => 'links.see',

                'name' => 'flat',
                'repeatable' => 'boolean',
                'description' => 'flat',
                'populated_at' => 'dates.datetime',
                'dossierable' => 'relations.belongsTo',
                // 'dossierrows' => 'relations.hasMany',
                'filecabinets' => 'relations.belongsToMany',

                'mySelfDelete' => 'links.delete'
            ]
        ];
	}
}