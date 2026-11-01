# Registro dei tipi di campo

`RowTypeRegistry` è il servizio condiviso per identificatori, classi, package,
nomi tradotti ed elenco dell'editor. `FormrowNamesTypeHelper` rimane compatibile
e delega al registro. Non vengono scansionati file PHP, nemmeno dei package esterni.
Gli identificatori base restano invariati, compreso `contract-file`.
I tipi legacy `operatorSelect` e `operatorsSelect` conservano le rispettive classi
e diventano indisponibili se manca Operators, evitando errori nei vecchi campi.

## Aggiungere un tipo da un package

Nel `register()` del ServiceProvider già dichiarato nell'autodiscovery Composer:

```php
use IlBronza\FileCabinet\Providers\RowTypes\RowTypeRegistry;

if (class_exists(RowTypeRegistry::class)) {
    $this->callAfterResolving(RowTypeRegistry::class, function ($registry) {
        $registry->register(
            'equipment',
            \IlBronza\Example\FileCabinet\RowTypes\FormrowEquipment::class,
            'ilbronza/example'
        );
    });
}
```

Il controllo deve precedere il caricamento delle classi di integrazione che
estendono FileCabinet. Un riferimento `::class` è una stringa, non carica la classe.
Non aggiungere dipendenze Composer obbligatorie fra questi package.

`callAfterResolving()` applica il contributo anche se il registro è già stato
risolto. Le registrazioni funzionano in entrambi gli ordini dei provider e non
dipendono dal merge della configurazione: funzionano anche con `config:cache`.

Ogni classe estende `BaseRow`, implementa i suoi metodi astratti e ha un costruttore
senza argomenti obbligatori. `getType()` deriva il nome dal basename della classe;
può essere ridefinito per un identificatore diverso. Il nome mostrato viene da
`getTranslatedName()`, ridefinibile nel package proprietario. L'identificatore
passato a `register()` è quello da salvare in `Formrow.type`.

Mantenere la variante singola e quella multipla in **file e classi distinti**.
Per riferimenti a modelli usare `BaseModelReferenceRow`: il package implementa
`referenceQuery($historical)` con il vincolo di appartenenza al proprietario del
dossier e `referenceLabel($model)`. La variante multipla può estendere quella
singola, usare `MultipleModelReferencesTrait`, impostare `$multiple = true` e
`$databaseField = 'text'`. Entrambe le query, anche quella storica, devono sempre
limitare i risultati all'azienda corretta.

Il registro è un singleton; le istanze dei tipi **non** lo sono. `make()`, l'helper,
`Formrow::getRowType()` e `Dossierrow::getRowType()` creano oggetti nuovi. Il contesto
di modello e Dossierrow non passa da un campo all'altro, anche se l'applicazione
registra la classe del tipo come singleton. Non conservare un'istanza di tipo
per riutilizzarla su più campi.

## Sostituzioni esplicite dell'applicazione

`register()` rifiuta sempre gli identificatori duplicati con una `LogicException`
che indica la classe già registrata. Per una sostituzione intenzionale:

```php
$this->callAfterResolving(RowTypeRegistry::class, function ($registry) {
    $registry->replace('room', \App\FileCabinet\FormrowRoom::class, 'app');
});
```

`replace()` ha precedenza indipendentemente dall'ordine dei provider. Le collisioni
fra due contributi `register()` restano errori anche in presenza di una sostituzione.
`info($id)`, `all()` e `choices()` espongono rispettivamente metadati del tipo,
metadati di tutti i tipi disponibili e coppie identificatore/nome per l'editor.

## Tipi forniti da Operators e Warehouse

| Identificatore | Package | Classe/file | Valore salvato |
| --- | --- | --- | --- |
| `clientOperator` | Operators | `src/FileCabinet/RowTypes/FormrowClientOperator.php` | UUID di ClientOperator in `string` |
| `clientOperators` | Operators | `src/FileCabinet/RowTypes/FormrowClientOperators.php` | array JSON di UUID in `text` |
| `room` | Warehouse | `src/FileCabinet/RowTypes/FormrowRoom.php` | UUID di Room in `string` |
| `rooms` | Warehouse | `src/FileCabinet/RowTypes/FormrowRooms.php` | array JSON di UUID in `text` |

Sono utilizzati i modelli del progetto risolti con `getProjectClassName()`.
Il proprietario supportato è un `Client`, anche una sua sottoclasse del progetto,
associato come `dossierable`. Per altri proprietari le scelte sono vuote e la
validazione rifiuta riferimenti: un altro contesto richiede una query esplicita.

I rapporti sono limitati da `ClientOperator.client_id` e, per nuove scelte, dallo
scope `current()`. L'etichetta è il nome dell'Operator collegato, mentre il valore
è sempre l'ID del **rapporto aziendale**. Le stanze sono limitate dall'azienda
della `Destination` collegata; stanze senza destinazione aziendale non sono scelte
valide. La `Structure` è un catalogo condiviso e non determina l'appartenenza.

Il salvataggio e il setter verificano sul server l'appartenenza anche quando il
chiamante omette la validazione opzionale. Gli ID restano stringhe; le selezioni
multiple accettano liste di stringhe e vengono serializzate come array JSON.
Questi tipi non chiamano `sync()` né modificano le relazioni dell'azienda.

Rapporti terminati e record archiviati rimangono leggibili. Le query storiche
escludono gli scope globali, ma mantengono il vincolo esplicito dell'azienda.
Solo gli ID già salvati nella **stessa Dossierrow** possono essere conservati e
compaiono fra le sue opzioni con l'indicazione «storico». Una volta rimossi dalla
selezione salvata, non possono essere aggiunti nuovamente come nuove scelte.
Un record eliminato fisicamente viene mostrato con il suo ID: non è disponibile
uno snapshot storico del nome e il riferimento non è più verificabile.

La compilazione dei nuovi riferimenti è online. L'esportazione offline mantiene
la propria lista di tipi supportati e li segnala come da completare online.

## Rimozione e ripristino di un package

Un identificatore sconosciuto risolve in `UnavailableRow`, un segnaposto di sola
lettura. Rendering online, tabelle, PDF e snapshot offline possono continuare a
mostrare un messaggio. Il registro conosce il package richiesto dai quattro tipi
sopra; per tipi terzi non conosciuti mostra comunque l'identificatore mancante.

Il segnaposto non sceglie una colonna di ripiego e non interpreta i valori.
Setter, cancellazione del valore e salvataggio del tipo non scrivono nulla;
la validazione rifiuta valori inviati per quel campo. Le normali compilazioni
possono salvare gli altri campi omettendo quello disabilitato. Tipo e parametri
di una Formrow già salvata non possono essere convertiti mentre il tipo manca.
La riconciliazione delle righe non rimuove righe di tipi indisponibili e il
condensamento le rifiuta prima di convertire dati.

Un campo non verificabile, anche facoltativo, non è considerato completo o
popolato. Un dossier che lo contiene non viene segnalato come completo.
Reinstallando il package, la risoluzione torna immediatamente al tipo reale e
usa gli stessi parametri e valori; non occorrono migrazioni o conversioni.

## Verifiche

I test `RowTypeRegistryTest`, `UnavailableRowTypeTest` e
`OptionalReferenceTypesTest` coprono compatibilità, collisioni, sostituzioni,
istanze indipendenti, conservazione, UUID, JSON, modelli configurati, aziende e
riferimenti storici. I test delle integrazioni vengono saltati se manca uno dei
due package opzionali.

La fixture standalone nasconde i namespace assenti in un processo nuovo e non
disinstalla package dall'applicazione di lavoro. Il bootstrap deve restituire un
Composer ClassLoader oppure definire `$loader`, mappando i package locali:

```sh
python3 tests/Fixtures/run-row-type-matrix.py PHP_EXECUTABLE BOOTSTRAP_FILE
```

La matrice esegue 26 combinazioni: package presenti/assenti, tutti gli ordini dei
tre provider, configurazione normale/in cache e registro risolto prima dei
contributi successivi. Per eseguire una singola fixture:

```sh
php tests/Fixtures/row-type-packages.php vendor/autoload.php Operators,FileCabinet --cached
```
