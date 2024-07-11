<ul class="uk-list">
    @foreach($dossiers->sortBy('sorting_index') as $dossier)
    <li id="dossier{{ $dossier->getKey() }}">
        @include('filecabinet::dossiers._populate')
    </li>
    @endforeach
</ul>
