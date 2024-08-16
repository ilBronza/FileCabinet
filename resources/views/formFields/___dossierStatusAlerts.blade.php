<div class="uk-width-1-1">
	<ol class="uk-margin-remove-bottom">
		@foreach($status['alerts'] as $alert)
			<li>
				{{ $alert }}
			</li>
		@endforeach
	</ol>
</div>