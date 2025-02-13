<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class FormrowByFormFieldsGroupParametersFile extends FieldsGroupParametersFile
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
                'sorting_index' => 'utilities.sorting',
                'description' => 'flat',
                'required' => 'editor.toggle',
                'form' => 'relations.belongsTo',
                'type' => 'filecabinet::rowType',

                'mySelfDelete' => 'links.delete'
            ]
        ];
	}
}