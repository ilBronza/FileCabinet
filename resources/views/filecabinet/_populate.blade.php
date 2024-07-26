<div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header">
        <div uk-grid>
            @if($targetModel = $filecabinet->getFilecabinetable())
            <span class="uk-h1 uk-width-1-1">
                <a href="{{ $targetModel->getEditUrl() }}">
                    <i uk-icon="file-edit"></i>
                </a>
                
                {{ $filecabinet->getFilecabinetable()->getLiveDrivemodelName() }}
            </span>
            @endif
            <span class="uk-h2 uk-width-1-1">{{ $filecabinet->getCategory()->getName() }}</span>        
            
            {!! $filecabinet->getPopulationNavbar()->render() !!}
        </div>

        @if(count($dossiers = $filecabinet->getDossiers()) > 1)
            @include('filecabinet::dossiers.menu')
        @endif

    </div>

    <div class="uk-card-body">
        @include('filecabinet::dossiers.populate')
    </div>


    @if($filecabinet->mustShowChildrenContent())
    <div class="uk-card-footer">
        <ul>
            @foreach($filecabinet->getChildren()->sortBy('sorting_index') as $child)
            <li>
                @include('filecabinet::filecabinet._populate', [
                    'filecabinet' => $child,
                    'seeChildrenContent' => $filecabinet->mustShowChildrenContent()
                    ])
            </li>
            @endforeach
        </ul>
    </div>
    @endif

</div>
