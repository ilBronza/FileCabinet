<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;
use IlBronza\FileCabinet\Models\Form;

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
				'mySelfDossiers' => [
					'type' => 'filecabinet::dossiers.dossiersByForm',
//					'form' => Form::getProjectClassName()::findCachedField('name', 'Patente')
				],
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