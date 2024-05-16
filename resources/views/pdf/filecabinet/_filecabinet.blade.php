<div class="uk-card uk-card-default uk-card-small sis-padding-left-xs">
	<div class="uk-card-header">
		<span class="uk-h{{ ($depth = ($depth ?? 1)) }}">

			@if(isset($buttonParentLevel))
			{{ $buttonParentLevel }}. 
			@endif

			{{ $filecabinet->getName() }}
		</span>
	</div>

		@if((count($filecabinet->getDossiers()))&&($alphabet = range('a', 'z')))
	<div class="uk-card-body">
		<div>
			@foreach($filecabinet->getDossiers() as $dossier)
			<div>
				@include('filecabinet::pdf.filecabinet._dossier', [
					'buttonParentLevel' => (isset($buttonParentLevel) ? $buttonParentLevel .'.' : '') . $alphabet[$loop->index % 26]
				])
			</div>
			@endforeach
		</div>
	</div>
		@endif

		@if(count($filecabinet->getChildren()))
	<div class="uk-card-footer">
		<div>
			@foreach($filecabinet->getChildren() as $_filecabinet)
			<div>
				@include('filecabinet::pdf.filecabinet._filecabinet', [
					'filecabinet' => $_filecabinet,
					'depth' => (($depth = ($depth + 1)) < 7) ? $depth : 6,
					'buttonParentLevel' => (isset($buttonParentLevel)) ? (($buttonParentLevel . '.') . $loop->index + 1) : ($loop->index + 1)
				])
			</div>
			@endforeach
		</div>
	</div>
		@endif

</div>