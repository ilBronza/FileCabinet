@extends('uikittemplate::pdf')

@section('content')


<style type="text/css">
.dossiertable th
{
	width: 30%;
}

.dossiertable td
{
	width: 20%;
}

.dossiertitle
{
	display: block;
	margin-top: 10px;
	text-transform: uppercase;
}

</style>

	@if($navbar)
		{!! $navbar->render() !!}
	@endif


	@include('filecabinet::pdf.filecabinet._filecabinet')

@endsection
