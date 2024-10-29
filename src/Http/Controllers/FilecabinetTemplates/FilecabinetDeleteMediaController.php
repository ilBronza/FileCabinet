<?php

namespace IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates;

use IlBronza\CRUD\Models\Media;
use IlBronza\CRUD\Traits\CRUDDeleteMediaTrait;

class FilecabinetDeleteMediaController extends FilecabinetTemplateCRUD
{
	use CRUDDeleteMediaTrait;
	
	public $allowedMethods = ['deleteMedia'];

	public function deleteMedia($filecabinetTemplate, Media $media)
	{
		return $this->_deleteMedia($filecabinetTemplate, $media);
	}

}