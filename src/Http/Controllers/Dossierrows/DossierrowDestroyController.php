<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossierrows;

use IlBronza\CRUD\Traits\CRUDDeleteTrait;

class DossierrowDestroyController extends DossierrowCRUD
{
	use CRUDDeleteTrait;

	public $allowedMethods = ['destroy'];

	public function destroy($dossierrow)
	{
		$dossierrow = $this->findModel($dossierrow);

		return $this->_destroy($dossierrow);
	}
}