@extends('uikittemplate::app')

@section('content')

@if(($viewMode ?? 'populate') === 'populate' && config('filecabinet.offline.enabled', true))
    <p><a class="uk-button uk-button-default" href="{{ route(config('filecabinet.routePrefix') . 'offline.entry', ['filecabinet' => $filecabinet->getKey()]) }}">Compila offline</a></p>
@endif

<div uk-grid>
    <div class="uk-width-large">
        {!! $navbar->render() !!}
    </div>
    <div class="uk-width-expand">
        @include('filecabinet::filecabinet._populate', ['level' => 1])
    </div>    
</div>


@endsection
