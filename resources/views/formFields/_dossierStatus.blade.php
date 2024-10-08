@include('formfield::uikit.formRowHeader')

@if($status = $field->getValue())

	<div @if($status['alerts']) uk-alert @endif uk-grid
		 class="uk-grid-collapse @if($status['alerts']) uk-alert uk-alert-danger @endif">
		<div class="uk-width-auto">
		<span uk-lightbox>
			<a data-type="iframe" href="{{ $field->getDossier()->getPopulateUrl() }}?iframed=true"
			   class="uk-float-left">
				<i class="fa-solid fa-pen-to-square"></i>
			</a>
		</span>
		</div>
		<div class="uk-width-small">
			{!! $field->getName() !!}
		</div>

		<div class="uk-width-expand">
			<div>
				@if(! $status['populated'])
					Da popolare <br/>
				@endif
			</div>
		</div>

		@include('filecabinet::formFields.__dossierStatusAlerts')

		@if($field->showMilestones())
		<div class="uk-width-1-1">
			@include('filecabinet::formFields._dossierStatusSchedules')
		</div>
			@endif

	</div>
@else
	Problemi di calcolo dello status
@endif

@include('formfield::uikit.formRowFooter')