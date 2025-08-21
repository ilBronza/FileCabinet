@extends('app')

@section('content')

    @include('filecabinet::dossiers._populate', [
	'viewMode' => $viewMode ?? 'edit'
	])

@endsection
