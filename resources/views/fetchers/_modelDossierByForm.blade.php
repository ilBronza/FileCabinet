{!! (new \IlBronza\UikitTemplate\Fetcher([
	'title' => Str::headline($formSlug),
	'url' => app('filecabinet')->route('dossiers.byModelForm', [
	'model' => $model->getMorphClass(),
	'key' => $model->getKey(),
	'form' => $formSlug
	])
	]))->render() !!}