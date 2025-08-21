{!! (new \IlBronza\UikitTemplate\Fetcher([
	'title' => Str::headline($categorySlug),
	'url' => app('filecabinet')->route('dossiers.byModelCategory.populate', [
	'model' => $model->getMorphClass(),
	'key' => $model->getKey(),
	'category' => $categorySlug
	])
	]))->render() !!}