<?php

namespace IlBronza\FileCabinet\Providers\DatatablesFields\Dossiers;

use IlBronza\Datatables\DatatablesFields\DatatableField;

class DatatableFieldDossierStatus extends DatatableField
{
	//TODO fare na roba fatta bene col fetcher

	public function hasModalStatusAlerts() : bool
	{
		return true;
	}

	public function getDossier()
	{
		return $this->value;
	}

	public function transformValue($value)
	{
		if(! $value)
			return null;

		$this->value = $value;

		return view('filecabinet::formfields.__dossierStatusAlerts', [
			'status' => $value->getStatus(),
			'field' => $this
		])->render();
	}
}