<?php

namespace IlBronza\FileCabinet\Tests\Offline;

use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Filecabinet;
use IlBronza\FileCabinet\Models\DossierFilecabinet;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\FileCabinet\Offline\OfflineAccess;
use IlBronza\FileCabinet\Offline\OfflineAssets;
use IlBronza\FileCabinet\Offline\OfflineQuestionnaire;
use IlBronza\FileCabinet\Offline\OfflineSync;
use IlBronza\FileCabinet\Tests\TestCase;
use Illuminate\Auth\GenericUser;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OfflineDossier extends Dossier
{
    public static bool $failOnTouch = false;
    public function getForeignKey() { return 'dossier_id'; }
    public function userCanSee($user = null) { return (bool) $this->can_edit; }
    public function userCanUpdate($user = null) { return (bool) $this->can_edit; }
    public function touch($attribute = null)
    {
        if (static::$failOnTouch) throw new RuntimeException('Simulated persistence failure');
        return parent::touch($attribute);
    }
}

class OfflineCabinet extends Filecabinet
{
    public function getForeignKey() { return 'filecabinet_id'; }
    public function userCanSee($user = null) { return (bool) $this->can_edit; }
    public function userCanUpdate($user = null) { return (bool) $this->can_edit; }
    public function getName() : ?string { return 'Questionario di prova'; }
}

class OfflineSyncTest extends TestCase
{
    protected OfflineQuestionnaire $questionnaire;
    protected OfflineSync $sync;
    protected OfflineAccess $access;
    protected ?string $assetsDirectory = null;

    protected function defineEnvironment($app) : void
    {
        parent::defineEnvironment($app);
        $app['config']->set('app.key', str_repeat('x', 32));
        $app['config']->set('filecabinet.routePrefix', 'ibFilecabinet');
        $app['config']->set('filecabinet.offline', ['enabled' => true, 'middleware' => [], 'authorizer' => null]);
        $app['config']->set('filecabinet.defaultRules', ['text' => ['max' => 255], 'textarea' => ['max' => 2048]]);
        $app['config']->set('filecabinet.models.dossier', ['class' => OfflineDossier::class, 'table' => 'filecabinets__dossiers']);
        $app['config']->set('filecabinet.models.filecabinet', ['class' => OfflineCabinet::class, 'table' => 'filecabinets__filecabinets']);
        $app['config']->set('filecabinet.models.dossierFilecabinet', ['class' => DossierFilecabinet::class, 'table' => 'filecabinets___dossier_filecabinets']);
    }

    protected function defineRoutes($router) : void
    {
        require __DIR__ . '/../../routes/offline.php';
    }

    protected function setUp() : void
    {
        parent::setUp();
        OfflineDossier::$failOnTouch = false;
        $this->app['view']->addNamespace('filecabinet', __DIR__ . '/../../resources/views');
        Schema::table(config('filecabinet.models.formrow.table'), function (Blueprint $table) {
            $table->boolean('read_only')->default(false);
        });
        Schema::create(config('filecabinet.models.dossier.table'), function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('form_id');
            $table->string('parent_id')->nullable();
            $table->integer('sorting_index')->default(0);
            $table->boolean('can_edit')->default(true);
            $table->timestamp('populated_at')->nullable();
            $table->string('populated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create(config('filecabinet.models.filecabinet.table'), function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('parent_id')->nullable();
            $table->boolean('can_edit')->default(true);
            $table->timestamp('populated_at')->nullable();
            $table->string('populated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create(config('filecabinet.models.dossierFilecabinet.table'), function (Blueprint $table) {
            $table->string('id')->nullable();
            $table->string('dossier_id');
            $table->string('filecabinet_id');
            $table->softDeletes();
            $table->timestamps();
        });
        (require __DIR__ . '/../../database/migrations/2026_10_19_000001_create_offline_sync_receipts_table.php')->up();
        DB::table(config('filecabinet.models.form.table'))->insert(['id' => 'form', 'name' => 'Sezione']);
        DB::table(config('filecabinet.models.dossier.table'))->insert(['id' => 'dossier', 'form_id' => 'form']);
        DB::table(config('filecabinet.models.filecabinet.table'))->insert(['id' => 'cabinet']);
        $this->actingAs(new GenericUser(['id' => 'user-one']));
        $this->access = new OfflineAccess();
        $this->questionnaire = new OfflineQuestionnaire($this->access);
        $this->sync = new OfflineSync($this->questionnaire, $this->access);
    }

    protected function row(string $type = 'text', mixed $value = null, array $attributes = []) : Dossierrow
    {
        $id = (string) Str::ulid();
        DB::table(config('filecabinet.models.formrow.table'))->insert(array_merge([
            'id' => $id, 'name' => 'Domanda', 'slug' => $id, 'form_id' => 'form', 'type' => $type,
            'parameters' => json_encode(['possibleValues' => [['value' => 'A'], ['value' => 'B']]]),
        ], $attributes));
        $column = match ($type) {
            'boolean' => 'boolean', 'integer', 'decimal' => 'decimal', 'date', 'datetime' => 'timestamp',
            'textarea', 'multipleSelect' => 'text', default => 'string',
        };
        DB::table(config('filecabinet.models.dossierrow.table'))->insert([
            'id' => $id, 'formrow_id' => $id, 'dossier_id' => 'dossier', $column => $value,
        ]);
        return Dossierrow::findOrFail($id);
    }

    protected function payload(Dossierrow $row, mixed $value) : array
    {
        $field = $this->questionnaire->field($row);
        return ['owner' => $this->access->owner(), 'operation_id' => (string) Str::uuid(), 'row_id' => $row->getKey(),
            'schema' => $field['schema'], 'revision' => $field['revision'], 'value' => $value];
    }

    protected function apply(array $payload) : array
    {
        return $this->sync->apply(OfflineDossier::findOrFail('dossier'), $payload);
    }

    public function testAnAcknowledgementLostInTransitCanBeRetriedWithoutRepeatingTheWrite() : void
    {
        $row = $this->row();
        $payload = $this->payload($row, 'Risposta offline');
        $first = $this->apply($payload);
        $this->assertSame('Risposta offline', $row->fresh()->string);
        // A later update must not be overwritten when the old request is retried.
        DB::table($row->getTable())->where('id', $row->getKey())->update(['string' => 'Nuova risposta']);
        $this->assertSame($first, $this->apply($payload));
        $this->assertSame('Nuova risposta', $row->fresh()->string);
        $this->assertSame(1, DB::table(OfflineSync::TABLE)->count());
    }

    public function testConcurrentServerChangesAreReturnedAsAConflict() : void
    {
        $row = $this->row('text', 'Originale');
        $payload = $this->payload($row, 'Locale');
        DB::table($row->getTable())->where('id', $row->getKey())->update(['string' => 'Server']);
        try { $this->apply($payload); $this->fail('Expected conflict'); }
        catch (HttpResponseException $e) {
            $this->assertSame(409, $e->getResponse()->getStatusCode());
            $this->assertSame('Server', $e->getResponse()->getData(true)['field']['value']);
        }
        $this->assertSame('Server', $row->fresh()->string);
        $this->assertSame(0, DB::table(OfflineSync::TABLE)->count());
    }

    public function testSchemaChangesAreDetectedEvenWhenTheValueIsUnchanged() : void
    {
        $row = $this->row();
        $payload = $this->payload($row, 'Locale');
        DB::table($row->formrow->getTable())->where('id', $row->formrow_id)->update(['required' => true]);
        $this->expectException(HttpResponseException::class);
        $this->apply($payload);
    }

    public function testValidationReadOnlyAndUnsupportedFieldsNeverWrite() : void
    {
        foreach ([['text', str_repeat('x', 256), []], ['text', 'Cambio', ['read_only' => true]], ['file', 'fake.pdf', []]] as [$type, $value, $attributes]) {
            $row = $this->row($type, null, $attributes);
            try { $this->apply($this->payload($row, $value)); $this->fail('Expected rejection'); }
            catch (ValidationException | HttpException $e) { $this->assertSame(422, $e instanceof ValidationException ? $e->status : $e->getStatusCode()); }
            $this->assertNull($row->fresh()->string);
        }
        $this->assertSame(0, DB::table(OfflineSync::TABLE)->count());
    }

    public function testNullFalseZeroDatesAndMultipleChoicesSurviveRoundTrip() : void
    {
        foreach ([['text', null], ['boolean', false], ['integer', '0'], ['decimal', '12.3456'], ['date', '2026-10-19'], ['datetime', '2026-10-19T10:25:30'], ['multipleSelect', ['A', 'B']]] as [$type, $value]) {
            $row = $this->row($type);
            $result = $this->apply($this->payload($row, $value));
            $this->assertEquals($value, $result['field']['value'], $type);
        }
        $row = $this->row('multipleSelect');
        $this->expectException(ValidationException::class);
        $this->apply($this->payload($row, ['not-an-option']));
    }

    public function testBlankNumbersAndDatesStayBlankInTheSnapshot() : void
    {
        foreach (['date', 'datetime', 'decimal', 'integer', 'boolean'] as $type)
            $this->assertNull($this->questionnaire->field($this->row($type))['value']);
    }

    public function testFailuresRollBackAnswersAndReceiptsTogether() : void
    {
        $row = $this->row();
        OfflineDossier::$failOnTouch = true;
        try { $this->apply($this->payload($row, 'Da annullare')); $this->fail('Expected failure'); }
        catch (RuntimeException $e) { $this->assertSame('Simulated persistence failure', $e->getMessage()); }
        $this->assertNull($row->fresh()->string);
        $this->assertSame(0, DB::table(OfflineSync::TABLE)->count());
    }

    public function testOperationIdsCannotBeReusedForDifferentAnswers() : void
    {
        $row = $this->row();
        $payload = $this->payload($row, 'Prima');
        $this->apply($payload);
        $payload['value'] = 'Seconda';
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Identificativo di invio già utilizzato.');
        $this->apply($payload);
    }

    public function testAnotherUserAndRevokedPermissionsCannotSyncOrReplay() : void
    {
        $row = $this->row();
        $payload = $this->payload($row, 'Prima');
        $this->apply($payload);
        $this->actingAs(new GenericUser(['id' => 'user-two']));
        try { $this->apply($payload); $this->fail('Expected wrong-user rejection'); }
        catch (HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
        $this->actingAs(new GenericUser(['id' => 'user-one']));
        DB::table(config('filecabinet.models.dossier.table'))->where('id', 'dossier')->update(['can_edit' => false]);
        try { $this->apply($payload); $this->fail('Expected revoked-access rejection'); }
        catch (HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
    }

    public function testARowCannotBeWrittenThroughAnotherDossier() : void
    {
        $row = $this->row();
        DB::table($row->getTable())->where('id', $row->getKey())->update(['dossier_id' => 'another']);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->apply($this->payload($row, 'No'));
    }

    public function testPublicShellContainsNoAnswersAndApiRequiresAuthentication() : void
    {
        Auth::forgetGuards();
        $this->get('/filecabinet-manager/offline/app')->assertOk()->assertSee('Questionario offline')->assertDontSee('csrf-token');
        $this->getJson('/filecabinet-manager/offline/api/session')->assertUnauthorized();
        $this->get('/filecabinet-manager/offline/sw.js')->assertOk()->assertHeader('Content-Type', 'application/javascript');
        $this->get('/filecabinet-manager/offline/app.js')->assertNotFound();
        $this->get('/filecabinet-manager/offline/secrets.php')->assertNotFound();
    }

    public function testHttpSnapshotAndSyncEnforceCabinetMembership() : void
    {
        $row = $this->row();
        $this->postJson('/filecabinet-manager/offline/api/sync/cabinet/dossier', $this->payload($row, 'No'))->assertNotFound();
        DB::table(config('filecabinet.models.dossierFilecabinet.table'))->insert(['dossier_id' => 'dossier', 'filecabinet_id' => 'cabinet']);
        $this->getJson('/filecabinet-manager/offline/api/snapshot/cabinet')->assertOk()->assertJsonPath('sections.0.fields.0.id', $row->getKey());
        $this->postJson('/filecabinet-manager/offline/api/sync/cabinet/dossier', $this->payload($row, 'Sì'))->assertOk()->assertJsonPath('field.value', 'Sì');
    }

    public function testApplicationAuthorizerAndPoliciesAreRespected() : void
    {
        config(['filecabinet.offline.authorizer' => fn ($model, $user) => false]);
        $this->getJson('/filecabinet-manager/offline/api/snapshot/cabinet')->assertForbidden();
        config(['filecabinet.offline.authorizer' => null]);
        Gate::policy(OfflineCabinet::class, RejectOfflinePolicy::class);
        $this->getJson('/filecabinet-manager/offline/api/snapshot/cabinet')->assertForbidden();
    }

    public function testSnapshotIncludesNestedDossiersAndRechecksTheirPermissions() : void
    {
        $row = $this->row();
        DB::table(config('filecabinet.models.dossierFilecabinet.table'))->insert(['dossier_id' => 'dossier', 'filecabinet_id' => 'cabinet']);
        DB::table(config('filecabinet.models.dossier.table'))->insert(['id' => 'child', 'parent_id' => 'dossier', 'form_id' => 'form']);
        DB::table($row->getTable())->where('id', $row->getKey())->update(['dossier_id' => 'child']);
        $this->getJson('/filecabinet-manager/offline/api/snapshot/cabinet')->assertOk()
            ->assertJsonPath('sections.1.id', 'child')->assertJsonPath('sections.1.fields.0.id', $row->getKey());
        DB::table(config('filecabinet.models.dossier.table'))->where('id', 'child')->update(['can_edit' => false]);
        $this->getJson('/filecabinet-manager/offline/api/snapshot/cabinet')->assertForbidden();
    }

    public function testEditingOneAnswerDoesNotClearOtherAnswersOrRequireTheWholeQuestionnaire() : void
    {
        $emptyRequired = $this->row('text', null, ['required' => true]);
        $untouched = $this->row('text', 'Conservare');
        $changed = $this->row('boolean');
        $this->apply($this->payload($changed, false));
        $this->assertNull($emptyRequired->fresh()->string);
        $this->assertSame('Conservare', $untouched->fresh()->string);
        $this->assertNull(OfflineDossier::findOrFail('dossier')->populated_at);
    }

    protected function publicAssets() : string
    {
        $this->assetsDirectory = sys_get_temp_dir() . '/filecabinet-assets-' . Str::uuid();
        mkdir($this->assetsDirectory . '/js', 0777, true);
        mkdir($this->assetsDirectory . '/css');
        $this->app->usePublicPath($this->assetsDirectory);
        file_put_contents(public_path('js/app.js'), 'console.log("release one");');
        file_put_contents(public_path('css/ilbronza.filecabinet.offline.css'), 'body { color: black; }');
        return $this->assetsDirectory;
    }

    protected function tearDown() : void
    {
        if ($this->assetsDirectory)
            $this->app['files']->deleteDirectory($this->assetsDirectory);
        parent::tearDown();
    }

    public function testShellAndWorkerUseTheSameVersionedMixAssets() : void
    {
        $this->publicAssets();
        file_put_contents(public_path('mix-manifest.json'), json_encode([
            '/js/app.js' => '/js/app.js?id=release-one',
            '/css/ilbronza.filecabinet.offline.css' => '/css/ilbronza.filecabinet.offline.css?id=style-one',
        ]));
        $this->get('/filecabinet-manager/offline/app')->assertOk()->assertSee('/js/app.js?id=release-one', false)
            ->assertSee('/css/ilbronza.filecabinet.offline.css?id=style-one', false)->assertDontSee('store.js');
        $assets = new OfflineAssets();
        $before = $assets->worker();
        $this->assertStringContainsString('app.js?id=release-one', $before);
        file_put_contents(public_path('mix-manifest.json'), json_encode(['/js/app.js' => '/js/app.js?id=release-two']));
        $this->assertNotSame($before, $assets->worker());
    }

    public function testUnversionedBuildsAreFingerprintedAndExtraChunksKeepTheirLoadOrder() : void
    {
        $this->publicAssets();
        file_put_contents(public_path('mix-manifest.json'), json_encode(['/js/app.js' => '/js/app.js']));
        config(['filecabinet.offline.assets.scripts' => ['js/manifest.js', 'js/vendor.js', 'js/app.js']]);
        config(['filecabinet.offline.assets.cache' => ['fonts/example.woff2']]);
        $assets = new OfflineAssets();
        $urls = $assets->urls();
        $this->assertStringEndsWith('/js/manifest.js', $urls['scripts'][0]);
        $this->assertStringEndsWith('/js/vendor.js', $urls['scripts'][1]);
        $this->assertStringContainsString('/js/app.js?id=', $urls['scripts'][2]);
        $this->assertStringContainsString('fonts', $assets->worker());
        $before = $assets->worker();
        file_put_contents(public_path('js/app.js'), 'console.log("release two");');
        $this->assertNotSame($before, $assets->worker());
    }
}

class RejectOfflinePolicy
{
    public function update($user, $model) { return false; }
}
