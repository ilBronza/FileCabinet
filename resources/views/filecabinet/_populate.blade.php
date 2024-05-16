<div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header">
        <div uk-grid>
            <span class="uk-h1">{{ $filecabinet->getCategory()->getName() }}</span>        
            
            {!! $filecabinet->getPopulationNavbar()->render() !!}
        </div>

        @if(count($dossiers = $filecabinet->getDossiers()) > 1)
        <div class="uk-width-1-2@s uk-width-2-5@m">
            <ul class="uk-nav uk-nav-default">
                @foreach($dossiers as $dossier)
                <li><a href="#dossier{{ $dossier->getKey() }}">{{ $dossier->getName() }}</a></li>
                @endforeach
            </ul>
        </div>
        @endif

    </div>

    <div class="uk-card-body">
        <ul class="uk-list">
            @foreach($dossiers as $dossier)
            <li id="dossier{{ $dossier->getKey() }}">
                @if($viewMode == 'populate')
                    {!! $dossier->renderAjaxForm() !!}
                @elseif($viewMode == 'show')
                    {!! $dossier->show() !!}
                @endif
            </li>
            @endforeach
        </ul>
    </div>


    @if($filecabinet->mustShowChildrenContent())
    <div class="uk-card-footer">
        <ul>
            @foreach($filecabinet->getChildren() as $child)
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
