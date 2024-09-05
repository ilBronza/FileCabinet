<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossiers;

use IlBronza\CRUD\Helpers\CrudRequestHelper;
use IlBronza\CRUD\Helpers\FileUpdaterHelpers\MediaAssociatorHelper;
use IlBronza\CRUD\Helpers\MediaPathGenerators\FileUpdaterHelper;
use IlBronza\CRUD\Traits\CRUDUpdateTrait;
use IlBronza\FileCabinet\Helpers\DossierPopulatorEditorHelper;
use IlBronza\FileCabinet\Helpers\DossierPopulatorHelper;
use IlBronza\FileCabinet\Models\Formrow;
use Illuminate\Http\Request;

use function config;
use function dd;
use function get_class_methods;

class DossierUpdateController extends DossierCRUD
{
	static $formFields = [
		'common' => [
			'default' => []
		]
	];

	use CRUDUpdateTrait;
    public $allowedMethods = ['update'];

	public function hasOriginalFilenameGenerator(string $mediaNameGeneratorHelperKey) : bool
	{
		return $mediaNameGeneratorHelperKey == 'originalFilename';
	}

	public function storeFile(Request $request)
	{
		$fieldname = $request->fieldname;

		$request->validate([
			'file' => 'required|file',
			'fieldname' => 'string|required',
			'index' => 'nullable|numeric',
			'uuid' => 'string|nullable',
			'multiple' => 'boolean|nullable'
		]);

		$formrow = Formrow::findBySlug($fieldname);
		$dossierrow = $this->dossier->getDossierrowByFormrow($formrow);

		if(! $mediaNameGeneratorHelperKey = $formrow->getSpecialParameter('nameType'))
			throw new \Exception('La riga non è configurata correttamente. Scegliere un tipo di generatore di nomi per i file');

		$parameters = [
			'multiple' => false,
			'attributeName' => 'string',
		];

		if(! $this->hasOriginalFilenameGenerator($mediaNameGeneratorHelperKey))
		{
			$mediaNameGenerator = config('filecabinet.media.mediaNameGenerators.' . $formrow->getSpecialParameter('nameType'));
			$nameGenerator = new $mediaNameGenerator($dossierrow, $formrow, $request->file('file'));
			$parameters['filename'] = $nameGenerator->generateName();
		}

		return MediaAssociatorHelper::associateFromRequest($dossierrow, 'file', $fieldname, null, $parameters);

		//			return $this->_uploadFile($request, 'update', 'string');
	}

    public function update(Request $request, string $dossier)
    {
        $this->dossier = $this->findModel($dossier);

		if(CrudRequestHelper::isFileUploadRequest($request))
			return $this->storeFile($request);

		if(CrudRequestHelper::isEditorUpdateRequest($request))
			return DossierPopulatorEditorHelper::populateByRequest($request, $this->dossier);

		if(CrudRequestHelper::isEditorReadRequest($request))
		{
			dd('eccol');
		}

		DossierPopulatorHelper::populateByRequest($request, $this->dossier);

		if($request->ajax())
			return response()->success();

		return redirect()->to($this->dossier->getDossierable()->getShowUrl());
    }
}
