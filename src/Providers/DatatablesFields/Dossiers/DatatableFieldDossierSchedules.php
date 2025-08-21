<?php

namespace IlBronza\FileCabinet\Providers\DatatablesFields\Dossiers;

use IlBronza\Datatables\DatatablesFields\DatatableField;

class DatatableFieldDossierSchedules  extends DatatableField
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

		return view('filecabinet::formfields._dossierStatusSchedules', [
			'status' => $value->getStatus(),
			'field' => $this
		])->render();
	}
}