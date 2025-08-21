<ul class="uk-list">
    @foreach($dossiers->sortBy('sorting_index') as $dossier)
    <li id="dossier{{ $dossier->getKey() }}">
        @php
        $indexes[$dossier->form_id] = ($indexes[$dossier->form_id] ?? 0) + 1;
        @endphp

        @include('filecabinet::dossiers._populate', [
            'childDossierIndex' => $indexes[$dossier->form_id]
            ])
    </li>
    @endforeach
</ul>
