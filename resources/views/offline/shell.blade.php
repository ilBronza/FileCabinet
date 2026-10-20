<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#173b35">
    <title>Questionario offline · FileCabinet</title>
    @foreach($assets['styles'] as $style)
        <link rel="stylesheet" href="{{ $style }}">
    @endforeach
    @foreach($assets['scripts'] as $script)
        <script defer src="{{ $script }}"></script>
    @endforeach
</head>
<body data-filecabinet-offline>
    <header>
        <div class="brand">FILECABINET <span>COMPILAZIONE OFFLINE</span></div>
        <h1 id="title">Il tuo questionario</h1>
        <p id="summary">Preparazione della copia sul dispositivo…</p>
        <div class="actions">
            <button id="sync" disabled>Sincronizza ora</button>
            <button id="refresh" class="secondary" disabled>Aggiorna questionario</button>
            <button id="export" class="secondary" disabled>Esporta copia</button>
            <a id="online" hidden>Apri versione online</a>
        </div>
    </header>
    <div class="statusbar"><span id="status" role="status" aria-live="polite">Apertura…</span><span id="offline-ready"></span></div>
    <p id="notice" role="alert" hidden></p>
    <div class="layout"><nav id="sections" aria-label="Sezioni del questionario"></nav><main id="questionnaire"></main></div>
    <footer>Le risposte sono conservate in questo browser e dispositivo. Usa un dispositivo personale; cancellando i dati del sito perdi le risposte non sincronizzate. Esporta una copia prima di cambiare dispositivo.</footer>
    <noscript>Per compilare il questionario occorre attivare JavaScript.</noscript>
</body>
</html>
