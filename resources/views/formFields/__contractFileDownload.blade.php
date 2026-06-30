@if($contractUrl = $field->getContractUrl())
	<a
		class="uk-icon-button uk-margin-small-right ib-contract-file-download"
		href="{{ $contractUrl }}"
		download
		target="_blank"
		rel="noopener"
		uk-tooltip="title: {{ __('filecabinet::fields.urlContractDownload') }}"
	>
		{!! FaIcon::inline('download') !!}
	</a>
@endif
