@if(count($elements = $categoryTree->recursiveChildren) > 0)
<ul>
	@foreach($elements as $child)
	<li>
		<a class="pdf-element-link" href="javascript:void(0)" data-slug="{{ $child->getSlug() }}">{{ $child->getName() }}</a>

		@include('filecabinet::filecabinetTemplates._categories', ['categoryTree' => $child])
	</li>
	@endforeach
</ul>
@endif