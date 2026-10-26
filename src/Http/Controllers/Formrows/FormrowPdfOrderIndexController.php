<?php

namespace IlBronza\FileCabinet\Http\Controllers\Formrows;

use IlBronza\Buttons\Button;
use IlBronza\FileCabinet\Http\Controllers\CRUDTraits\PdfOrderingIndexTrait;
use IlBronza\FileCabinet\Models\Form;
use Illuminate\Database\Eloquent\Builder;

class FormrowPdfOrderIndexController extends FormrowIndexController
{
    use PdfOrderingIndexTrait;

    public $allowedMethods = ['index', 'storeMassReorder'];
    public bool $useIndexCache = false;

    protected function getPdfForm(): Form
    {
        return Form::getProjectClassName()::visibleInPdf()->findOrFail(request()->route('form'));
    }

    public function getIndexFieldsArray()
    {
        return config('filecabinet.models.formrow.fieldsGroupsFiles.pdfOrder')::getTracedFieldsGroup();
    }

    protected function getPdfOrderingQuery(): Builder
    {
        return $this->getModelClass()::visibleInPdf()->where('form_id', $this->getPdfForm()->getKey());
    }

    protected function getPdfOrderingStoreUrl(): string
    {
        return $this->getPdfForm()->getKeyedRoute('formrows.storePdfOrder');
    }

    protected function getPdfOrderingTitle(): string
    {
        return __('filecabinet::buttons.pdfFormrowsOrder') . ' — ' . $this->getPdfForm()->getName();
    }

    public function addIndexButtons()
    {
        $this->table->addButton(Button::create([
            'href' => $this->getPdfForm()->getShowUrl(),
            'text' => 'filecabinet::buttons.backToForm',
            'icon' => 'arrow-left',
        ]));
    }
}
