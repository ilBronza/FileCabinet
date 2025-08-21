<div class="uk-width-1-2@s uk-width-2-5@m">
    <ul class="uk-nav uk-nav-default">
        @foreach($dossiers->sortBy('sorting_index') as $dossier)
        <li>
            <a href="#dossier{{ $dossier->getKey() }}">{{ $dossier->getName() }}</a>
            @if($children = $dossier->getChildren())
            <div class="uk-margin-small-left">                
                @include('filecabinet::dossiers.menu', ['dossiers' => $children])
            </div>
            @endif
        </li>
        @endforeach
    </ul>
</div>
