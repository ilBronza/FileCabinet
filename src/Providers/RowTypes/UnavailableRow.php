<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FormField\Fields\TextFormField;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Interfaces\FormfieldModelCompatibilityInterface;

/** Read-only placeholder. It deliberately never selects or writes a value column. */
class UnavailableRow extends BaseRow
{
	use StandardCheckFieldValidityParametersTrait;

	public ?bool $multiple = false;
	static $fieldType = 'text';

	public function __construct(public string $identifier, public string $message) {}

	public function isAvailable() : bool { return false; }
	public function getDatatableValueMethod() : string { return 'renderValueByFormrow'; }
	public function getDefaultRules() : array { return ['prohibited']; }
	public function buildRules(FormfieldModelCompatibilityInterface $model, ?Dossierrow $dossierrow = null) : array
	{
		return $this->getDefaultRules();
	}
	public function getFormField() : FormField { return new TextFormField(); }
	public function getDossierrowValue(Dossierrow $dossierrow) { return $this->message; }
	public function getShowValue(mixed $databaseValue) : mixed { return $this->message; }
	public function renderValueForView($value) : ?string { return e($this->message); }
	public function getDatabaseField() : string
	{
		throw new \LogicException($this->message);
	}
	public function setDossierrowValue(Dossierrow $dossierrow, $value) {}
	public function emptyRowValue(Dossierrow $dossierrow) { return false; }
	public function storeDossierrow(Dossierrow $dossierrow, mixed $value, bool $validate = false) : bool { return false; }
	public function _storeDossierrow(Dossierrow $dossierrow, mixed $value, bool $validate = false) : bool { return false; }
}
