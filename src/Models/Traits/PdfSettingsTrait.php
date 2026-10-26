<?php

namespace IlBronza\FileCabinet\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait PdfSettingsTrait
{
    public function isPdfVisible(): bool
    {
        return $this->pdf_show === null || (bool) $this->pdf_show;
    }

    public function getPdfSortingIndex(): ?int
    {
        return $this->pdf_sorting_index ?? $this->sorting_index;
    }

    public function scopeVisibleInPdf(Builder $query): Builder
    {
        $column = $query->getModel()->qualifyColumn('pdf_show');

        return $query->where(function (Builder $query) use ($column) {
            $query->whereNull($column)->orWhere($column, true);
        });
    }
}
