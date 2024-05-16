<div class="dossier uk-card uk-card-default uk-card-small">
	<div class="uk-card-header">
		<span>
			@if(isset($buttonParentLevel))
				{{ $buttonParentLevel }}
			@endif

			{{ $dossier->getName() }}
		</span>
	</div>
	<div class="uk-card-body">
		@if($dossier->hasPdfRenderTemplate())
			{!! $dossier->renderByPdfTemplate() !!}
		@else
		<table class="table table-bordered">
			<tbody>
			@foreach($dossier->getDossierrows() as $dossierrow)
				<tr>
					<th class="definition">{{ $dossierrow->getName() }}</th>
					<td class="value">{!! $dossierrow->renderFormfieldValue() !!}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		@endif
	</div>
</div>
