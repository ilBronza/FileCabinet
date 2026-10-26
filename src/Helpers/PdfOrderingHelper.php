<?php

namespace IlBronza\FileCabinet\Helpers;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PdfOrderingHelper
{
    public static function orderedVisible(Collection $elements, ?Closure $definition = null): Collection
    {
        $definition ??= fn ($element) => $element;

        return $elements->filter(fn ($element) => $definition($element)->isPdfVisible())
            ->sortBy(fn ($element) => $definition($element)->getPdfSortingIndex() . $element->created_at?->timestamp)
            ->values();
    }

    public static function indexElements(Builder $query): Collection
    {
        return static::orderedVisible($query->orderBy($query->getModel()->getQualifiedKeyName())->get())
            ->each(function ($element, $index) {
                // Unique display positions let RowReorder handle null or duplicate overrides.
                $element->pdf_sorting_position = $index + 1;
            });
    }

    public static function store(Builder $query, array $indexes): void
    {
        $query->getConnection()->transaction(function () use ($query, $indexes) {
            $elements = static::orderedVisible(
                $query->orderBy($query->getModel()->getQualifiedKeyName())->lockForUpdate()->get()
            );
            $positions = $elements->mapWithKeys(fn ($element, $index) => [$element->getKey() => $index + 1])->all();

            foreach ($indexes as $id => $position) {
                if (! array_key_exists($id, $positions) || ! is_numeric($position)
                    || (int) $position != $position || $position < 1 || $position > $elements->count()) {
                    throw ValidationException::withMessages(['indexes' => __('filecabinet::pdfOrdering.invalidOrder')]);
                }

                $positions[$id] = (int) $position;
            }

            if (count(array_unique($positions)) !== count($positions)) {
                throw ValidationException::withMessages(['indexes' => __('filecabinet::pdfOrdering.invalidOrder')]);
            }

            // Persist the full visible sequence: unchanged rows may still inherit a base index.
            foreach ($elements as $element) {
                $element->pdf_sorting_index = $positions[$element->getKey()];
                if ($element->isDirty('pdf_sorting_index')) {
                    $element->save();
                }
            }
        });
    }
}
