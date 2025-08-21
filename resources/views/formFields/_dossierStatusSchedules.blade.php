@if(count($status['schedules'] ?? []) > 0)
	<div class="schedule-milestones">
		@foreach($status['schedules'] as $schedule)
			@include('formfield::show.uikit._milestone', ['percentage' => $schedule['percentage']])
		@endforeach
	</div>
@endif
