<?php

namespace IlBronza\FileCabinet\Http\Controllers\CRUDTraits;

use IlBronza\FileCabinet\Helpers\PdfOrderingHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait PdfOrderingIndexTrait
{
    abstract protected function getPdfOrderingQuery(): Builder;
    abstract protected function getPdfOrderingStoreUrl(): string;
    abstract protected function getPdfOrderingTitle(): string;

    public function getIndexElements()
    {
        return PdfOrderingHelper::indexElements($this->getPdfOrderingQuery());
    }

    public function getPageLength()
    {
        return -1;
    }

    public function beforeRenderIndex()
    {
        $this->table->setCaption($this->getPdfOrderingTitle());
        $this->table->setDragAndDropColumnIntestation('pdf_sorting_position');
        $this->table->setDragAndDropSelector('pdf_sorting_position');
        $this->table->setDragAndDropStoringReorderUrl($this->getPdfOrderingStoreUrl());
    }

    public function storeMassReorder(Request $request)
    {
        $data = $request->validate([
            'indexes' => 'required|array|min:1|max:65535',
            'indexes.*' => 'required|integer|min:1|max:65535',
        ]);

        PdfOrderingHelper::store($this->getPdfOrderingQuery(), $data['indexes']);

        return ['success' => true];
    }
}
