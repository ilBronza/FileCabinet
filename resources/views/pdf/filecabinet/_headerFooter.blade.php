@php($headerHeight = $headerHeight ?? 90)

<style type="text/css">
@page
{
    margin: {{ $headerHeight }}px 20px 45px;
}

body
{
    margin: 0 10px;
}

.filecabinet-pdf-header
{
    position: fixed;
    top: -{{ $headerHeight - 20 }}px;
    left: 0;
    right: 0;
}

.filecabinet-pdf-header img
{
    @if($logoMaxWidth !== null)
    max-width: {{ $logoMaxWidth }}px;
    @else
    max-width: none;
    @endif
    @if($logoMaxHeight !== null)
    max-height: {{ $logoMaxHeight }}px;
    @else
    max-height: none;
    @endif
    width: auto;
    height: auto;
    vertical-align: middle;
}

.filecabinet-pdf-header table
{
    width: 100%;
    table-layout: auto;
    border-collapse: collapse;
}

.filecabinet-pdf-header td
{
    padding: 0;
    border: 0;
    vertical-align: middle;
}

.filecabinet-pdf-header .filecabinet-pdf-logo
{
    width: 1%;
    padding-right: 20px;
    text-align: left;
}

.filecabinet-pdf-header .filecabinet-pdf-title
{
    text-align: right;
}

.filecabinet-pdf-title h1
{
    display: block;
    margin: 0;
    padding: 0;
    line-height: 1.2;
}

.filecabinet-pdf-title h1 *
{
    margin: 0;
    padding: 0;
    font-size: inherit;
    line-height: inherit;
}

.filecabinet-pdf-footer
{
    position: fixed;
    bottom: -30px;
    left: 0;
    right: 0;
    height: 20px;
    text-align: right;
}

.filecabinet-pdf-page-number:before
{
    content: counter(page);
}
</style>

<header class="filecabinet-pdf-header">
    <table>
        <tr>
            @if($logo)
                <td class="filecabinet-pdf-logo">
                    <img src="{{ $logo }}" alt="Logo" />
                </td>
            @endif
            <td class="filecabinet-pdf-title">
                <h1>{!! $title !!}</h1>
            </td>
        </tr>
    </table>
</header>

<footer class="filecabinet-pdf-footer">
    <span class="filecabinet-pdf-page-number"></span>
</footer>
