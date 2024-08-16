{!! (new \IlBronza\UikitTemplate\Fetcher(['url' => app('filecabinet')->route('dossiers.byModelCategory', [
	'model' => $model->getMorphClass(),
	'key' => $model->getKey(),
	'category' => $categorySlug
	])]))->render() !!}