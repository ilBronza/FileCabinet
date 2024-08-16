<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use IlBronza\FileCabinet\Models\Dossierrow;

trait FormrowWithEventTrait
{
	abstract public function performBeforeStoreAction(Dossierrow $dossierrow, mixed $value);
	abstract public function performAfterStoreAction(Dossierrow $dossierrow, mixed $value);

	public function storeDossierrow(Dossierrow $dossierrow, mixed $value, bool $validate = false) : bool
	{
		$this->performBeforeStoreAction($dossierrow, $value);

		$result = $this->_storeDossierrow($dossierrow, $value, $validate);

		$this->performAfterStoreAction($dossierrow, $value);

		return $result;
	}
}