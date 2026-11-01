<?php

namespace IlBronza\FileCabinet\Offline;

use IlBronza\FileCabinet\Helpers\FilecabinetConsecutivenessCheckerHelper;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Filecabinet;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class OfflineQuestionnaire
{
    private const TYPES = ['text', 'textarea', 'boolean', 'integer', 'decimal', 'date', 'datetime', 'singleSelect', 'multipleSelect'];

    public function __construct(protected OfflineAccess $access) {}

    public function dossiers(Filecabinet $cabinet) : Collection
    {
        $this->access->authorize($cabinet);
        if ($target = $cabinet->getFilecabinetable())
            $this->access->authorize($target);

        abort_unless(FilecabinetConsecutivenessCheckerHelper::checkConsecutiveness($cabinet) === true, 409,
            'Completare prima i fascicoli precedenti.');

        $result = collect();
        $visit = function (Dossier $dossier) use (&$visit, $result) {
            if ($result->has($dossier->getKey()))
                return;
            $this->access->authorize($dossier);
            if ($target = $dossier->getDossierable())
                $this->access->authorize($target);
            $result->put($dossier->getKey(), $dossier);
            foreach ($dossier->children()->orderBy('sorting_index')->get() as $child)
                $visit($child);
        };
        foreach ($cabinet->dossiers()->orderBy('sorting_index')->get() as $dossier)
            $visit($dossier);

        return $result;
    }

    public function snapshot(Filecabinet $cabinet) : array
    {
        $sections = [];
        foreach ($this->dossiers($cabinet) as $dossier)
        {
            $dossier->load('form', 'dossierrows.formrow');
            $sections[] = [
                'id' => (string) $dossier->getKey(),
                'title' => $dossier->getName(),
                'description' => strip_tags($dossier->getDescription() ?? ''),
                'fields' => $dossier->getSortedDossierrows()->map(fn ($row) => $this->field($row))->values()->all(),
            ];
        }

        return [
            'format' => 1,
            'owner' => $this->access->owner(),
            'id' => (string) $cabinet->getKey(),
            'title' => $cabinet->getName(),
            'downloadedAt' => now()->toIso8601String(),
            'sections' => $sections,
        ];
    }

    public function field(Dossierrow $row) : array
    {
        $formrow = $row->getFormrow();
        $type = Str::camel($formrow->getType());
		$available = $row->getRowType()->isAvailable();
        $supported = $available && in_array($type, self::TYPES, true);
        $editable = $supported && ! $formrow->isReadOnly();
        $options = [];
        $value = null;
        $rules = [];

        if ($supported)
        {
            $rowType = $row->getRowType();
            $rules = $row->getFormfieldRules();
            // Read the stored value: date/decimal presentation getters coerce empty values.
            $value = $row->getAttribute($rowType->getDatabaseField());
            if ($type === 'multipleSelect')
                $value = $value === null || is_array($value) ? $value : json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            elseif ($type === 'boolean')
                $value = $value === null ? null : (bool) $value;
            elseif ($type === 'date')
                $value = $value === null ? null : substr($value, 0, 10);
            elseif ($type === 'datetime')
                $value = $value === null ? null : str_replace(' ', 'T', substr($value, 0, 19));
            if (in_array($type, ['singleSelect', 'multipleSelect'], true))
                foreach ($rowType->getPossibleValuesArray() as $key => $label)
                    $options[] = ['value' => (string) $key, 'label' => (string) $label];
        }

        $schema = $this->hash([$formrow->getAttributes(), $rules, $options]);
        return [
            'id' => (string) $row->getKey(),
            'dossierId' => (string) $row->dossier_id,
            'label' => $formrow->getName(),
            'description' => strip_tags($formrow->description ?? ''),
            'type' => $type,
            'required' => $formrow->isRequired(),
            'editable' => $editable,
            'reason' => ! $available ? $row->getRowType()->message : (! $supported ? 'Da completare online' : ($editable ? null : 'Sola lettura')),
            'options' => $options,
            'value' => $value,
            'schema' => $schema,
            'revision' => $this->hash([$row->getAttributes(), $schema]),
        ];
    }

    public function hash(mixed $value) : string
    {
        return hash('sha256', json_encode($value, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION));
    }
}
