@extends('uikittemplate::pdf')

@section('content')


{!! $navbar->render() !!}

@include('filecabinet::pdf.filecabinet._filecabinet')


@endsection
