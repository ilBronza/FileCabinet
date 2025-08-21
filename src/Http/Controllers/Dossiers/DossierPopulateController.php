<?php

namespace IlBronza\FileCabinet\Http\Controllers\Dossiers;

use function compact;

class DossierPopulateController extends DossierCRUD
{
    public string $viewMode = 'populate';

    public $allowedMethods = [
        'populate'
    ];

    public function populate(string $dossier)
    {
        $dossier = $this->findModel($dossier);

		$dossier->load('dossierrows.formrow', 'form');

		return view('filecabinet::dossiers.populateSingle', compact("dossier"));
	}

	public function populateStore(Request $request, string $dossier)
	{
		$dossier = $this->findModel($dossier);

		$dossier->populate(request()->all());

		return redirect()->route('dossiers.show', $dossier->id);
	}
}
