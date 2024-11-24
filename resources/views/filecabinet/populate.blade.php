@extends('app')

@section('content')

<div uk-grid>
    <div class="uk-width-large">
        {!! $navbar->render() !!}
    </div>
    <div class="uk-width-expand">
        @include('filecabinet::filecabinet._populate', ['level' => 1])
    </div>    
</div>


@endsection
