<div class="uk-card uk-card-small">
    <div class="uk-card-header">
        <h3>Creato il: {{ $filecabinet->getCreatedAt() }}</h3>
        <h4>
            @if($filecabinet->isPopulated())
                <a href="{{ $filecabinet->getShowUrl() }}">Completato</a>
            @else
                <a href="{{ $filecabinet->getPopulateUrl() }}">Da completare</a>
            @endif
        </h4>
    </div>
    <div class="uk-card-body">
    </div>
</div>