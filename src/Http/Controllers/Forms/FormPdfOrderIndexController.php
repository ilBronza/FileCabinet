<?php

namespace IlBronza\FileCabinet\Http\Controllers\Forms;

use IlBronza\Buttons\Button;
use IlBronza\FileCabinet\Http\Controllers\CRUDTraits\PdfOrderingIndexTrait;
use Illuminate\Database\Eloquent\Builder;

class FormPdfOrderIndexController extends FormIndexController
{
    use PdfOrderingIndexTrait;

    public $allowedMethods = ['index', 'storeMassReorder'];
    public $avoidCreateButton = true;
    public bool $useIndexCache = false;

    public function getIndexFieldsArray()
    {
        return config('filecabinet.models.form.fieldsGroupsFiles.pdfOrder')::getTracedFieldsGroup();
    }

    protected function getPdfOrderingQuery(): Builder
    {
        return $this->getModelClass()::visibleInPdf();
    }

    protected function getPdfOrderingStoreUrl(): string
    {
        return route(config('filecabinet.routePrefix') . 'forms.storePdfOrder');
    }

    protected function getPdfOrderingTitle(): string
    {
        return __('filecabinet::buttons.pdfFormsOrder');
    }

    public function addIndexButtons()
    {
        $this->table->addButton(Button::create([
            'href' => route(config('filecabinet.routePrefix') . 'forms.index'),
            'text' => 'filecabinet::buttons.backToForms',
            'icon' => 'arrow-left',
        ]));
    }
}
