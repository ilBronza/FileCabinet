@if(($count = count($status['alerts'] ?? [])) > 0)

	@if($field->hasModalStatusAlerts())
		<a uk-tooltip='@include('filecabinet::formFields.___dossierStatusAlerts')'
		   class="uk-button uk-button-small uk-button-danger" href="javascript:void(0)"
		   uk-toggle="target: #dossier-statusmodal-{{ $field->getId() }}-{{$field->getDossier()->getKey()}}" type="button">
			&nbsp;<i class="fa-solid fa-exclamation-triangle"></i>
			{{ $count }}
		</a>

		<!-- This is the modal -->
		<div id="dossier-statusmodal-{{ $field->getId() }}-{{$field->getDossier()->getKey()}}" uk-modal>
			<div class="uk-modal-dialog">
				<button class="uk-modal-close-default" type="button" uk-close></button>
				<div class="uk-modal-header">
					<h2 class="uk-modal-title">Status documento<br/>{{ $field->getDossier()->getName() }}</h2>
				</div>
				<div class="uk-modal-body">
					@include('filecabinet::formFields.___dossierStatusAlerts')
				</div>
			</div>
		</div>

	@else

		<div class="uk-width-1-1">
			@include('filecabinet::formFields.___dossierStatusAlerts')
		</div>
	@endif

@endif
