<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class DossierRelatedFieldsGroupParametersFile extends DossierFieldsGroupParametersFile
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

                'form' => 'relations.belongsTo',
                'repeatable' => 'boolean',
                'description' => 'flat',
				'dossierrows' => 'relations.hasMany',
				'all_schedules' => [
					'type' => 'iterators.each',
					'childParameters' => [
						'type' => 'utilities.milestone',
						'property' => 'percentage_validity',
					]
				],
                'mySelfRowValues.dossierrows' => [
                    'type' => 'iterators.each',
                    'childParameters' => [
                        'type' => 'function',
                        'function' => 'getValue'
                    ],
                ],
                'filecabinets' => 'relations.belongsToMany',

                'mySelfDelete' => 'links.delete'
            ]
        ];
	}
}