<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class FilecabinetTemplateFieldsGroupParametersFile extends FieldsGroupParametersFile
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

                'category' => 'relations.belongsTo',
                'models' => [
                    'type' => 'jsonObjects',
                    'properties' => [
                        'model' => [],
                        'event' => []
                    ]
                ],

                'mySelfDelete' => 'links.delete'
            ]
        ];
	}
}