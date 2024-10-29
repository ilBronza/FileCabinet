<?php

namespace IlBronza\FileCabinet\Models\Traits;

use IlBronza\Buttons\Helpers\DefaultButtonsCreatorHelper;

trait DossierRenderTrait
{
	public function renderAjaxForm()
	{
		$ibForm = $this->buildIbForm();

		// $ibForm->addNavbarButton(
		// 	DefaultButtonsCreatorHelper::getShowButton($this)
		// );

		return $ibForm->_renderAjax();
	}

	public function render()
	{
		$ibForm = $this->buildIbForm();

		// $ibForm->addNavbarButton(
		// 	DefaultButtonsCreatorHelper::getShowButton($this)
		// );

		return $ibForm->_render();
	}

	public function renderForPdf()
	{
		return view('filecabinet::pdf.filecabinet._dossier', [
			'dossier' => $this
		]);
	}

	public function show()
	{
		$ibForm = $this->buildIbForm();

		$ibForm->addNavbarButton(
			DefaultButtonsCreatorHelper::getEditButton($this)
		);

		return $ibForm->_renderShow();
	}
}
