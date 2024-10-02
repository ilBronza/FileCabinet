<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use IlBronza\FileCabinet\Models\Dossierrow;

interface FormrowListInterface
{
	public function getPossibleValuesArray(Dossierrow $dossierrow = null) : array;
}