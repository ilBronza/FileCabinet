<div class="dossier uk-card uk-card-default uk-card-small">
	<div class="uk-card-header">
		<span class="dossiertitle">
			@if($filecabinet)
			{!! $filecabinet->getPdfTitle() !!}
			@endif

{{-- 			@if(isset($buttonParentLevel))
				{{ $buttonParentLevel }}
			@endif
 --}}
 			-

			{{ $dossier->getName() }}
		</span>
	</div>
	<div class="uk-card-body">
		@if($dossier->hasPdfRenderTemplate())
			{!! $dossier->renderByPdfTemplate() !!}
		@else
		<table class="table table-bordered dossiertable uk-width-1-1">
			<tbody>
			<tr>
			@foreach($dossier->getDossierrows() as $dossierrow)
				@if(($loop->index > 0)&&(($colCount = ($loop->index) % 2) == 0))
				</tr><tr>
				@endif
					<th class="definition">{{ $dossierrow->getName() }}</th>
					<td class="value" @if(($loop->index > 0)&&($loop->last)&&(($colCount ?? 0) < 2)) colspan="3" @endif>{!! $dossierrow->renderFormfieldValue() !!}</td>
			@endforeach
			</tr>
			</tbody>
		</table>
		@endif
	</div>
</div>
