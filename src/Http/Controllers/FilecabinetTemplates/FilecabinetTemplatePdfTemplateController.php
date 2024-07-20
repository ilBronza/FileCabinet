<?php

namespace IlBronza\FileCabinet\Http\Controllers\FilecabinetTemplates;

class FilecabinetTemplatePdfTemplateController extends FilecabinetTemplateEditUpdateController
{
    public function getGenericParametersFile() : ? string
    {
        //FilecabinetTemplatePdfTemplateFieldsetsParameters
        return config('filecabinet.models.filecabinetTemplate.parametersFiles.pdfTemplate');
    }
}
