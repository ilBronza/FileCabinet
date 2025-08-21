<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossierrows;

use IlBronza\CRUD\Models\Media;

class DossierrowDeleteMediaController extends DossierrowCRUD
{
	public $allowedMethods = ['deleteMedia'];

	public function deleteMedia($dossierrow, Media $media)
	{
		$dossierrow = $this->findModel($dossierrow);
		
		if (! $dossierrow->is($media->model))
			return [
				'success' => false
			];

		$media->delete();

		$dossierrow->emptyRowValue();

		return [
			'success' => true
		];
	}

}