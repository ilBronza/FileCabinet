<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\FileCabinet\Models\Filecabinet;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;

/** Attach every supplied instance without creating or changing any dossier. */
class AttachExistingDossiersHelper
{
    public static function attach(Filecabinet $filecabinet, Collection $dossiers): int
    {
        if (! $filecabinet->exists || $filecabinet->trashed() || ! $filecabinet->filecabinetable_id || ! $filecabinet->filecabinetable_type) {
            throw new InvalidArgumentException('An active filecabinet with an owner is required.');
        }
        foreach ($dossiers as $dossier) {
            if ((string) $dossier->dossierable_id !== (string) $filecabinet->filecabinetable_id
                || $dossier->dossierable_type !== $filecabinet->filecabinetable_type
                || $dossier->getConnection()->getName() !== $filecabinet->getConnection()->getName()
                || ! $dossier->exists || $dossier->trashed()) {
                throw new InvalidArgumentException('Only active dossiers belonging to the filecabinet owner may be attached.');
            }
        }

        $connection = $filecabinet->getConnection();
        return $connection->transaction(function () use ($connection, $filecabinet, $dossiers): int {
            // Serialize concurrent attachment runs for this cabinet, including empty pivots.
            $filecabinet->newQuery()->whereKey($filecabinet->getKey())->lockForUpdate()->firstOrFail();
            $table = $filecabinet->dossiers()->getTable();
            $attached = 0;
            foreach ($dossiers->unique(fn ($dossier) => $dossier->getKey()) as $dossier) {
                $query = $connection->table($table)->where('filecabinet_id', $filecabinet->getKey())->where('dossier_id', $dossier->getKey());
                if ($existing = $query->first()) {
                    if ($existing->deleted_at !== null) {
                        $query->update(['deleted_at' => null, 'updated_at' => now()]);
                        $attached++;
                    }
                    continue;
                }
                $connection->table($table)->insert([
                    'id' => (string) Str::uuid(), 'filecabinet_id' => $filecabinet->getKey(),
                    'dossier_id' => $dossier->getKey(), 'created_at' => now(), 'updated_at' => now(),
                ]);
                $attached++;
            }
            $filecabinet->unsetRelation('dossiers');
            return $attached;
        });
    }
}
