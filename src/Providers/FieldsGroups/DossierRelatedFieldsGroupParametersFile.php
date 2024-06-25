<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class DossierRelatedFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
            'translationPrefix' => 'filecabinet::fields',
            'fields' => 
            [
                'mySelfPrimary' => 'primary',
                'mySelfEdit' => 'links.edit',
                'mySelfSee' => 'links.see',

                'form' => 'relations.belongsTo',
                'repeatable' => 'boolean',
                'description' => 'flat',
                'dossierrows' => 'relations.hasMany',
                'dossierable' => 'relations.belongsTo',
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