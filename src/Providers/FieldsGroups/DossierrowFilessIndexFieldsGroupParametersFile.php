<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class DossierrowFilessIndexFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
            'translationPrefix' => 'filecabinet::fields',
            'fields' => 
            [
                'mySelfPrimary' => 'primary',
//                'mySelfEdit' => 'links.edit',
//                'mySelfSee' => 'links.see',

                'formrow.name' => 'flat',
                'formrow.form.name' => 'flat',

                'dossier.client.name' => 'flat',
                // 'mySelfValue' => '_fn_getShowValue',

                // 'dossier' => 'relations.belongsTo',
                // 'formrow' => 'relations.belongsTo',
                // 'formrow.type' => 'flat',

//                'mySelfDelete' => 'links.delete'
            ]
        ];
	}
}