<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class FormrowFieldsGroupParametersFile extends FieldsGroupParametersFile
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

                'name' => 'flat',
                'slug' => 'flat',
                'description' => 'flat',
                'required' => 'boolean',
                'form' => 'relations.belongsTo',
                'type' => 'filecabinet::rowType',

                'mySelfDelete' => 'links.delete'
            ]
        ];
	}
}