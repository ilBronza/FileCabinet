{!! (new \IlBronza\UikitTemplate\Fetcher([
	'title' => Str::headline($categorySlug),
	'url' => app('filecabinet')->route('dossiers.byModelCategory', [
	'model' => $model->getMorphClass(),
	'key' => $model->getKey(),
	'category' => $categorySlug
	])
	]))->render() !!}