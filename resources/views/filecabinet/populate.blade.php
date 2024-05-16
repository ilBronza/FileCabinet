@extends('app')

@section('content')

<div uk-grid>
    <div class="uk-width-large">
        {!! $navbar->render() !!}
    </div>
    <div class="uk-width-expand">
        @include('filecabinet::filecabinet._populate')
    </div>    
</div>


@endsection
