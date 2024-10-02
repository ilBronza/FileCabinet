<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Providers\RowTypes\BaseModelRelationRow;
use IlBronza\Products\Models\Quotations\Quotationrow;

class FormrowQuotationrowSelect extends BaseModelRelationRow
{
	public function getShowValue(mixed $databaseValue) : mixed
	{
		return Quotationrow::gpc()::find($databaseValue)?->getName();
	}

	public function getPossibleValuesArray(Dossierrow $dossierrow = null) : array
	{
		$elements = Quotationrow::gpc()::with('sellable', 'quotation')->whereIn('sellable_supplier_id', $dossierrow->getDossier()->getDossierable()->getSellableSuppliersIds())->get();

		$result = [];

		foreach ($elements as $element)
			$result[$element->getKey()] = $element->getFullname();

		return $result;
	}

	public function getSpecialParametersFieldsetParameters() : array
	{
		return [];
	}
}