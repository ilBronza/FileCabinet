<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class DossierByFormFieldsGroupParametersFile extends FieldsGroupParametersFile
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

				'mySelfStatus' => 'filecabinet::dossiers.dossierStatus',
				'mySelfSchedules' => 'filecabinet::dossiers.dossierSchedules',

                'populated_at' => 'dates.datetime',
                'dossierable' => 'relations.belongsTo',
                // 'dossierrows' => 'relations.hasMany',
                'filecabinets' => 'relations.belongsToMany',

                'mySelfDelete' => 'links.delete'
            ]
        ];
	}
}