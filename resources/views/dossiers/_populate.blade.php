@if($viewMode == 'populate')
    {!! $dossier->renderAjaxForm() !!}
@elseif($viewMode == 'edit')
    {!! $dossier->render() !!}
@elseif($viewMode == 'show')
    {!! $dossier->show() !!}
@endif

@if($children = $dossier->getChildren())
<div class="uk-margin-left uk-margin-top">
    
    @include('filecabinet::dossiers.populate', ['dossiers' => $children])

</div>
@endif
