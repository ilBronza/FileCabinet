@extends('app')

@section('content')

<div class="uk-card uk-card-primary">
    <div class="uk-card-header">
        <h1>@lang('filecabinet::messages.elementHasAlreadyFilecabinetByCategory', [
            'element' => $model->getName(),
            'category' => $category->getName()
        ])</h1>
    </div>
    <div class="uk-card-body">
        <ul class="uk-list">
            @foreach($filecabinets as $filecabinet)
                <li>@include('filecabinet::filecabinet._teaser')</li>
            @endforeach
        </ul>
    </div>
    <div class="uk-card-footer">
        <span class="uk-h3">
            @lang('filecabinet::filecabinet.ifYouWantToAddItAgainAndCreateANewFilecabinetClickHere', ['link' => $model->getAttachFormByCategoryUrl($category, ['force' => true])]);
        </span>
    </div>
</div>

@endsection
