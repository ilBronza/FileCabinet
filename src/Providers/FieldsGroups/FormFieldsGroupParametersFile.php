<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class FormFieldsGroupParametersFile extends FieldsGroupParametersFile
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
                'mySelfLink' => 'links.clone',

                'name' => 'flat',
                'slug' => 'flat',
                'parent' => 'relations.belongsTo',
                'sorting_index' => 'editor.numeric',
                'children' => 'relations.hasMany',
                'repeatable' => 'boolean',
                'description' => 'flat',
                'formrows' => 'relations.hasMany',
                'category' => 'relations.belongsTo',
                // 'categories' => 'relations.belongsToMany',

                'mySelfDelete' => 'links.delete'
            ]
        ];
	}
}