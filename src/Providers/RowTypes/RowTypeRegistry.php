<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use InvalidArgumentException;
use LogicException;

class RowTypeRegistry
{
	protected array $types = [];
	protected array $replacements = [];

	// Metadata survives removal of these optional packages; no integration classes are loaded.
	protected array $knownPackages = [
		'operatorSelect' => 'ilbronza/operators',
		'operatorsSelect' => 'ilbronza/operators',
		'clientOperator' => 'ilbronza/operators',
		'clientOperators' => 'ilbronza/operators',
		'contracttype' => 'ilbronza/operators',
		'room' => 'ilbronza/warehouse',
		'rooms' => 'ilbronza/warehouse',
		'roomType' => 'ilbronza/warehouse',
	];

	public function __construct()
	{
		foreach ([
			'boolean' => Rows\FormrowBoolean::class,
			'contract-file' => Rows\FormrowContractFile::class,
			'date' => Rows\FormrowDate::class,
			'datetime' => Rows\FormrowDatetime::class,
			'decimal' => Rows\FormrowDecimal::class,
			'expirationDate' => Rows\FormrowExpirationDate::class,
			'file' => Rows\FormrowFile::class,
			'integer' => Rows\FormrowInteger::class,
			'json' => Rows\FormrowJson::class,
			'modelAttribute' => Rows\FormrowModelAttribute::class,
			'modelMethod' => Rows\FormrowModelMethod::class,
			'multipleSelect' => Rows\FormrowMultipleSelect::class,
			'operatorSelect' => Rows\FormrowOperatorSelect::class,
			'operatorsSelect' => Rows\FormrowOperatorsSelect::class,
			'price' => Rows\FormrowPrice::class,
			'quotationrowSelect' => Rows\FormrowQuotationrowSelect::class,
			'relation' => Rows\FormrowRelation::class,
			'singleSelect' => Rows\FormrowSingleSelect::class,
			'text' => Rows\FormrowText::class,
			'textarea' => Rows\FormrowTextarea::class,
			'userSelect' => Rows\FormrowUserSelect::class,
		] as $id => $class)
		{
			// Legacy core types also need a safe fallback if Operators is removed.
			if (in_array($id, ['operatorSelect', 'operatorsSelect'], true)
				&& ! class_exists(\IlBronza\Operators\OperatorsServiceProvider::class))
				continue;

			$this->register($id, $class, 'ilbronza/filecabinet');
		}
	}

	public function register(string $id, string $class, ?string $package = null) : void
	{
		if (isset($this->types[$id]))
			throw new LogicException("Row type [{$id}] is already registered by [{$this->types[$id]['class']}]; use replace() for an application override.");

		$this->types[$id] = $this->definition($id, $class, $package);
	}

	/** Application overrides take precedence even when registered before a package. */
	public function replace(string $id, string $class, ?string $package = null) : void
	{
		$this->replacements[$id] = $this->definition($id, $class, $package);
	}

	protected function definition(string $id, string $class, ?string $package) : array
	{
		if ($id === '' || ! is_subclass_of($class, BaseRow::class) || (new \ReflectionClass($class))->isAbstract())
			throw new InvalidArgumentException("Invalid row type [{$id}]: [{$class}] must be a concrete BaseRow.");

		return ['class' => $class, 'package' => $package];
	}

	public function has(string $id) : bool
	{
		return isset($this->replacements[$id]) || isset($this->types[$id]);
	}

	public function info(string $id) : array
	{
		$definition = $this->replacements[$id] ?? $this->types[$id] ?? null;

		return [
			'id' => $id,
			'available' => $definition !== null,
			'class' => $definition['class'] ?? null,
			'package' => $definition['package'] ?? $this->knownPackages[$id] ?? null,
			'name' => $definition ? $definition['class']::getTranslatedName() : $this->missingMessage($id),
		];
	}

	public function missingMessage(string $id) : string
	{
		$package = $this->knownPackages[$id] ?? null;
		return trans('filecabinet::rowTypes.unavailable', ['type' => $id])
			. ($package ? ' ' . trans('filecabinet::rowTypes.requiredPackage', ['package' => $package]) : '');
	}

	public function make(string $id) : BaseRow
	{
		$class = $this->replacements[$id]['class'] ?? $this->types[$id]['class'] ?? null;

		// Never resolve through the container: even an application's singleton binding
		// must not share mutable form/dossier context between fields.
		return $class ? new $class() : new UnavailableRow($id, $this->missingMessage($id));
	}

	public function all() : array
	{
		$result = [];
		foreach (array_keys(array_replace($this->types, $this->replacements)) as $id)
			$result[$id] = $this->info($id);

		return $result;
	}

	public function choices() : array
	{
		return array_map(fn (array $info) => $info['name'], $this->all());
	}
}
