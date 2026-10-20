# FileCabinet

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require ilbronza/filecabinet
```

## Usage

## Form options

``` bash

automatically_creatable: 
    if true, the form will be automatically created when the dossier is created in bulk from category
automatic_creation_checker_method: 
    if set, the form will check the target if the form is automatically creatable. 
    Ex. if you populate this parameter with 'isBornInEu' and you try to attach automatically this form to an Operator, 
    the operatro will call 'isBornInEu' method to check if the form is automatically creatable

```

## Datatable Fields

``` bash

        //link to dossiers by form. If the datatable row is a form, omit the form parameter
		'mySelfDossiers' => [
			'type' => 'filecabinet::dossiers.dossiersByForm',
			//ONLY IF row value is not a Form
			'form' => Form::getProjectClassName()::findCachedField('name', 'Patente')
		],

        //filecabinets list with populate/show link
        'rootFilecabinets' => 'filecabinet::filecabinets.filecabinetsStatus',

        //create a button to add filecabinets based on defined category
        'mySelfAddTest' => [
            'type' => 'filecabinet::attachForm.attachFormByCategory',
            'category' => Category::getProjectClassName()::findCachedField('name', 'Collaudo')
        ],

        //create a button to add filecabinets based on root filecabinets categories
        'mySelfAddTests' => 'filecabinet::attachForm.attachFormByRoots',

```



## Offline questionnaires

The population page has a **Compila offline** link. Open it while connected to
prepare a local copy of the selected filecabinet's existing dossiers, including
nested dossiers. Each dossier is a section. Child **filecabinets** are prepared
separately from their own population page, respecting forced population order.

### Enable in the host application

1. Update the package and run `php artisan migrate`. The new
   `filecabinets__offline_sync_receipts` table makes retries idempotent.
2. Serve the application over HTTPS (localhost is also supported).
3. Publish/include/build the package assets as described below. If application
   routes/configuration are cached, rebuild them after updating.
4. Open the population page, choose **Compila offline**, and wait for
   **Pagina disponibile offline** before disconnecting. Bookmark this page to
   reopen the same questionnaire on the same browser/device.

`filecabinet.offline.enabled` defaults to `true`; set it to `false` to remove the
routes and entry link (rebuild cached routes after configuration changes).

### Webpack / Laravel Mix assets

FileCabinet follows the other IlBronza packages' asset workflow. Publish sources
into the host application's `resources` directory:

```bash
php artisan vendor:publish --tag=filecabinet.assets
```

Include the package entry in `resources/js/app.js`:

```js
require('./ilbronza.filecabinet.offline.js');
```

It requires its IndexedDB module internally and only initializes on an element
with `data-filecabinet-offline`, after the DOM is ready. It can therefore be
included in the shared application bundle without initializing questionnaires
or opening a local database on other pages. No globals or separate `store.js`
script tag are required.

In the existing `webpack.mix.js`, keep the application's JavaScript compilation
and add the standalone questionnaire stylesheet to the build:

```js
mix.copy(
    'resources/css/ilbronza.filecabinet.offline.css',
    'public/css/ilbronza.filecabinet.offline.css'
).version();
```

Run the host application's normal build, for example `npm run development` or
`npm run production`. On package upgrades, republish updated sources (using
`--force` if replacing previously published package assets) and rebuild.
With an `@ilbronza` webpack alias pointing to `vendor/ilbronza`, the entry can
instead be required directly from
`@ilbronza/filecabinet/resources/assets/js/ilbronza.filecabinet.offline.js`;
copy the stylesheet from that package's `resources/assets/css` directory too.

The offline shell loads `js/app.js` and
`css/ilbronza.filecabinet.offline.css` by default. The `offline.assets` settings
can select another bundle or list runtime/vendor chunks in their required order:

```php
'assets' => [
    'scripts' => ['js/manifest.js', 'js/vendor.js', 'js/app.js'],
    'styles' => ['css/ilbronza.filecabinet.offline.css'],
    'cache' => [], // Additional public resources needed offline, e.g. fonts.
],
```

For a host bundle with initialization tied to its normal layout, a dedicated
bundle is also supported: compile `resources/js/ilbronza.filecabinet.offline.js`
to `public/js` and set `offline.assets.scripts` to
`['js/ilbronza.filecabinet.offline.js']`.

`mix-manifest.json` supplies the exact versioned URLs used both by Blade and the
service worker. Builds without Mix versioning use file-content fingerprints.
Resources are served on the application's origin and only the configured public
resources are cached. The worker's revision changes automatically with its code
or asset URLs. Deploy the compiled files and manifest together.

The service worker itself has a separate execution context and URL:
`filecabinet-manager/offline/sw.js`. Laravel serves its small script with the
current cache list under the correct URL scope; it must not be imported into the
page's `app.js`. The application and IndexedDB code are compiled by webpack.

### Saving and synchronization

Every input is saved in an IndexedDB transaction. Only changed answers are sent,
with automatic retries on reconnect, page focus and every 30 seconds while the
page is visible. **Sincronizza ora** retries immediately. An immutable operation
ID and payload are persisted before sending; the server records its receipt in
the same database transaction as the answer. A lost response can therefore be
retried without repeating writes. Edits made while an earlier answer is being
sent remain queued for the next request.

Synchronization validates each changed row using its FileCabinet rules, keeps
other answers intact, and invokes the existing dossier completion hooks. It
checks model permissions, application `update` policies and the dossier's
membership in the selected filecabinet. Permissions and the current user's
identity are checked again on every synchronization, including retries.

A changed row or question definition returns a conflict with the server value.
The user chooses **Usa risposta del server** or **Conserva la mia risposta**;
the latter rebases the local answer and still requires server validation.
**Aggiorna questionario** downloads additions/changes without discarding pending
answers. Deleted fields with pending answers remain visible for recovery.
**Esporta copia** downloads a readable JSON backup, including pending answers
and input still in memory after a local storage failure. Backups are for manual
recovery; this version does not automatically import them.

### Supported fields and limits

- Editable offline: text, textarea, boolean, integer, decimal, date, datetime,
  single select and multiple select. Existing instances of repeatable fields
  can be edited individually.
- Files, contract files, price, expiration dates with schedule actions,
  relationships, model attributes/methods and custom types are marked
  **Da completare online**. Creation/deletion of dossiers or repeatable instances
  remains online. Read-only fields stay read-only on the server as well.
- A partially completed questionnaire can be saved locally; invalid changed
  answers remain local with their validation error until corrected.
- Synchronization runs with the page active or when reopened. No guarantee of
  background transmission after the browser is closed is made.
- Storage is scoped to this site's browser profile and user. A shared device's
  local browser data is accessible to its users; use personal devices for
  confidential questionnaires. Browser data clearing or storage eviction can
  remove unsynchronized work. Avoid private browsing, whose storage may disappear
  when the private session ends. Persistent storage is requested on a best-effort
  basis; export a backup for important work.
- Only generic HTML/CSS/JS enter the service worker cache. Sessions, CSRF tokens
  and API responses are never cached there. The service worker controls only
  the offline URL scope. Asset fingerprints automatically identify a new
  shell/asset set; the old worker remains until its tabs close.
- Keep synchronization receipts while offline clients may retry old operations.
  Removing them shortens the idempotency window. Their table must use the same
  database connection as the dossier models.

### Application authorization

The package checks `userCanSee()` and `userCanUpdate()` on filecabinets, dossiers
and their associated models. If a model has a Laravel policy, its `update`
ability is also required. For associated ordinary Eloquent models without these
methods, configure a policy or `filecabinet.offline.authorizer` callback.
The optional callback receives `model` and `user` and must return `true`.
`filecabinet.offline.middleware` can add the host application's tenant/access
middleware to all authenticated offline endpoints. Include any restrictions
normally enforced only in custom population controllers here or in policies.

### Verification

The PHP suite includes snapshot/authentication, validation, conflicts, null/false/
zero values, rollback and replay tests in `tests/Offline/OfflineSyncTest.php`.
Run the package's PHPUnit suite with its normal development dependencies.

```bash
node --test tests/Offline/store.test.cjs
node tests/Offline/browser.test.cjs
```

The browser test needs webpack, Playwright and an installed Chrome. It builds
an actual production bundle in a temporary directory, then starts an ephemeral
loopback server with a fixture API. It uses the compiled package assets and
IndexedDB/service worker, and exercises offline reload, reconnect, conflicts,
retries, account changes, storage failures, a 1,006-field questionnaire, backups
and responsive layout. It does not contact the
host application's database. The PHP tests exercise the real Laravel handlers
and database writes separately; a deployment still needs a smoke test with the
host application's authentication, policies and custom models.

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Attach existing dossiers

`AttachExistingDossiersHelper::attach($filecabinet, $dossiers)` attaches every supplied
active dossier instance belonging to the cabinet owner. It does not create or modify
dossiers. Repeated calls skip active pivots and restore soft-deleted pivots. The
return value counts new and restored attachments. Validation rejects mixed owners,
connections, archived dossiers, or an unsaved/archived cabinet before writing.

```php
use IlBronza\FileCabinet\Helpers\AttachExistingDossiersHelper;

$attached = AttachExistingDossiersHelper::attach($cabinet, $existingDossiers);
```

Use this for migrations and custom filing screens that must preserve multiple
instances of the same form. Existing template creation behavior is unchanged.
Coverage: `tests/Feature/AttachExistingDossiersTest.php`.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [author name][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ilbronza/filecabinet.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ilbronza/filecabinet.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ilbronza/filecabinet/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/ilbronza/filecabinet
[link-downloads]: https://packagist.org/packages/ilbronza/filecabinet
[link-travis]: https://travis-ci.org/ilbronza/filecabinet
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/ilbronza
[link-contributors]: ../../contributors
