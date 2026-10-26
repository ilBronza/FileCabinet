<?php

namespace IlBronza\FileCabinet\Tests\Feature;

use IlBronza\FileCabinet\FileCabinet;
use IlBronza\FileCabinet\Helpers\FilecabinetPdfTreeHelper;
use IlBronza\FileCabinet\Helpers\PdfOrderingHelper;
use IlBronza\FileCabinet\Http\Controllers\Forms\FormPdfOrderIndexController;
use IlBronza\FileCabinet\Http\Controllers\Formrows\FormrowPdfOrderIndexController;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Filecabinet as Cabinet;
use IlBronza\FileCabinet\Models\Form;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\FileCabinet\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

if (! class_exists(\App\Http\Controllers\Controller::class)) {
    require_once __DIR__ . '/../Fixtures/AppController.php';
}

class PdfValueRow extends Dossierrow
{
    public function renderFormfieldValue(): ?string { return 'value-' . $this->getKey(); }
}

class PdfOrderingTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);
        $app['config']->set('filecabinet', require __DIR__ . '/../../config/filecabinet.php');
        $app->singleton('filecabinet', fn () => new FileCabinet());
        $app['config']->set('app.key', str_repeat('x', 32));
    }

    protected function defineRoutes($router): void
    {
        require __DIR__ . '/../../routes/filecabinet.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->app['view']->addNamespace('filecabinet', __DIR__ . '/../../resources/views');
        $this->app['translator']->addNamespace('filecabinet', __DIR__ . '/../../resources/lang');

        Schema::table((new Form())->getTable(), function (Blueprint $table) {
            $table->unsignedInteger('sorting_index')->nullable();
            $table->string('slug')->nullable();
        });
        foreach (['000001_add_pdf_sorting_index', '000002_add_pdf_show'] as $migration) {
            (require __DIR__ . '/../../database/migrations/2026_10_23_' . $migration . '_to_forms_and_formrows_tables.php')->up();
        }
    }

    private function form(string $id, array $attributes = []): Form
    {
        DB::table((new Form())->getTable())->insert(array_merge([
            'id' => $id, 'name' => $id, 'slug' => $id, 'sorting_index' => 10,
            'created_at' => '2026-01-01 00:00:00',
        ], $attributes));
        return Form::findOrFail($id);
    }

    private function row(string $id, string $form, array $attributes = []): Formrow
    {
        DB::table((new Formrow())->getTable())->insert(array_merge([
            'id' => $id, 'name' => $id, 'slug' => $id, 'type' => 'text', 'form_id' => $form,
            'sorting_index' => 10, 'created_at' => '2026-01-01 00:00:00',
        ], $attributes));
        return Formrow::findOrFail($id);
    }

    private function dossier(Form $form, array $rows = [], string $id = 'dossier'): Dossier
    {
        $dossier = new Dossier();
        $dossier->forceFill(['id' => $id, 'created_at' => '2026-01-01 00:00:00']);
        $dossier->setRelation('form', $form);
        $dossier->setRelation('children', collect());
        $dossier->setRelation('dossierrows', collect($rows));
        return $dossier;
    }

    private function valueRow(Formrow $definition, string $id, string $created = '2026-01-01 00:00:00'): PdfValueRow
    {
        $row = new PdfValueRow();
        $row->forceFill(['id' => $id, 'created_at' => $created]);
        $row->setRelation('formrow', $definition);
        return $row;
    }

    public function test_migrations_default_to_null_and_can_be_rolled_back(): void
    {
        $form = $this->form('form');
        $row = $this->row('row', 'form');
        foreach ([$form, $row] as $model) {
            $this->assertNull($model->pdf_show);
            $this->assertNull($model->pdf_sorting_index);
            $this->assertTrue($model->isPdfVisible());
        }
        foreach (['000002_add_pdf_show', '000001_add_pdf_sorting_index'] as $migration) {
            (require __DIR__ . '/../../database/migrations/2026_10_23_' . $migration . '_to_forms_and_formrows_tables.php')->down();
        }
        foreach ([$form, $row] as $model) {
            $this->assertFalse(Schema::hasColumn($model->getTable(), 'pdf_show'));
            $this->assertFalse(Schema::hasColumn($model->getTable(), 'pdf_sorting_index'));
            $this->assertTrue(Schema::hasColumn($model->getTable(), 'sorting_index'));
        }
    }

    public function test_indexes_filter_visibility_and_do_not_leak_rows_from_other_forms(): void
    {
        $this->form('visible', ['pdf_show' => true]);
        $this->form('default');
        $this->form('hidden', ['pdf_show' => false]);
        $forms = $this->app->make(FormPdfOrderIndexController::class)->getIndexElements();
        $this->assertSame(['default', 'visible'], $forms->modelKeys());
        $this->assertSame([1, 2], $forms->pluck('pdf_sorting_position')->all());
        $this->assertNull(Form::find('default')->pdf_sorting_index);

        $this->row('default-row', 'default');
        $this->row('visible-row', 'default', ['pdf_show' => true]);
        $this->row('hidden-row', 'default', ['pdf_show' => false]);
        $this->row('foreign-row', 'visible', ['pdf_show' => true]);
        $this->assertSame(['default-row', 'visible-row'], Formrow::where('form_id', 'default')->visibleInPdf()->orderBy('id')->pluck('id')->all());

        $request = Request::create(route('ibFilecabinetforms.formrows.pdfOrder', ['form' => 'default']));
        $route = Route::getRoutes()->match($request);
        $request->setRouteResolver(fn () => $route);
        $this->app->instance('request', $request);
        $rows = $this->app->make(FormrowPdfOrderIndexController::class)->getIndexElements();
        $this->assertSame(['default-row', 'visible-row'], $rows->modelKeys());
    }

    public function test_dragging_forms_persists_only_pdf_order_and_normalizes_duplicate_base_indexes(): void
    {
        foreach (['a', 'b', 'c'] as $id) $this->form($id);
        $this->form('hidden', ['pdf_show' => false, 'pdf_sorting_index' => 90]);
        $this->postJson(route('ibFilecabinetforms.storePdfOrder'), ['indexes' => ['b' => 1, 'a' => 2]])
            ->assertOk()->assertJson(['success' => true]);

        $this->assertSame(['b', 'a', 'c'], Form::visibleInPdf()->orderBy('pdf_sorting_index')->pluck('id')->all());
        $this->assertSame([2, 1, 3], Form::whereIn('id', ['a', 'b', 'c'])->orderBy('id')->pluck('pdf_sorting_index')->all());
        $this->assertSame([10], Form::pluck('sorting_index')->unique()->values()->all());
        $this->assertSame(90, Form::find('hidden')->pdf_sorting_index);
    }

    public function test_reorder_rejects_hidden_unknown_or_invalid_positions_atomically(): void
    {
        $this->form('a');
        $this->form('b');
        $this->form('hidden', ['pdf_show' => false]);
        foreach ([['a' => 2], ['a' => 2, 'b' => 2], ['a' => 0], ['a' => 3], ['a' => -1], ['a' => 'bad'], ['hidden' => 1], ['missing' => 1]] as $indexes) {
            $this->postJson(route('ibFilecabinetforms.storePdfOrder'), ['indexes' => $indexes])->assertUnprocessable();
            $this->assertSame(0, Form::whereNotNull('pdf_sorting_index')->count());
        }
    }

    public function test_row_reorder_is_scoped_to_visible_rows_of_the_selected_form(): void
    {
        $this->form('form');
        $this->form('other');
        $this->row('a', 'form');
        $this->row('b', 'form');
        $this->row('hidden', 'form', ['pdf_show' => false]);
        $this->row('foreign', 'other');
        $url = route('ibFilecabinetforms.formrows.storePdfOrder', ['form' => 'form']);
        $this->postJson($url, ['indexes' => ['foreign' => 1]])->assertUnprocessable();
        $this->postJson($url, ['indexes' => ['hidden' => 1]])->assertUnprocessable();
        $this->postJson($url, ['indexes' => ['b' => '1', 'a' => '2']], ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();
        $this->assertSame(['b', 'a'], Formrow::where('form_id', 'form')->visibleInPdf()->orderBy('pdf_sorting_index')->pluck('id')->all());
        $this->assertNull(Formrow::find('foreign')->pdf_sorting_index);
        $this->assertNull(Formrow::find('hidden')->pdf_sorting_index);
        $this->assertSame([10], Formrow::pluck('sorting_index')->unique()->values()->all());

        DB::table((new Form())->getTable())->where('id', 'form')->update(['pdf_show' => false]);
        $this->postJson($url, ['indexes' => ['a' => 1, 'b' => 2]])->assertNotFound();
    }

    public function test_pdf_uses_overrides_including_zero_and_creation_time_but_not_update_time(): void
    {
        $form = $this->form('form');
        $first = $this->row('first', 'form', ['sorting_index' => 99, 'pdf_sorting_index' => 0]);
        $second = $this->row('second', 'form', ['sorting_index' => 1]);
        $hidden = $this->row('hidden', 'form', ['pdf_show' => false, 'pdf_sorting_index' => 0]);
        $old = $this->valueRow($second, 'old');
        $old->updated_at = '2026-10-23 12:00:00';
        $dossier = $this->dossier($form, [
            $this->valueRow($second, 'new', '2026-02-01 00:00:00'),
            $this->valueRow($hidden, 'hidden-value'),
            $old,
            $this->valueRow($first, 'first-value'),
        ]);
        $this->assertSame(['first-value', 'old', 'new'], $dossier->getPdfDossierrows()->pluck('id')->all());
        $html = view('filecabinet::pdf.filecabinet._dossier', compact('dossier'))->render();
        $this->assertStringNotContainsString('hidden', $html);
        $this->assertLessThan(strpos($html, 'value-old'), strpos($html, 'value-first-value'));
        $this->assertLessThan(strpos($html, 'value-new'), strpos($html, 'value-old'));
        $this->assertSame(3, substr_count($html, 'class="definition"'));

        $form->pdf_show = false;
        $this->assertSame('', trim(view('filecabinet::pdf.filecabinet._dossier', compact('dossier'))->render()));
    }

    public function test_pdf_menu_and_body_use_the_same_filtered_order_without_changing_base_indexes(): void
    {
        $a = $this->dossier($this->form('a', ['sorting_index' => 1, 'pdf_sorting_index' => 9]), [], 'a');
        $b = $this->dossier($this->form('b', ['sorting_index' => 9, 'pdf_sorting_index' => 0]), [], 'b');
        $hidden = $this->dossier($this->form('hidden', ['pdf_show' => false]), [], 'hidden');
        foreach ([$a, $b, $hidden] as $dossier) {
            $dossier->sorting_index = $dossier->getForm()->sorting_index;
        }
        $cabinet = new Cabinet();
        $cabinet->setRelation('dossiers', collect([$a, $hidden, $b]));
        $cabinet->setRelation('recursiveChildren', collect());
        $this->assertSame(['a', 'b', 'hidden'], $cabinet->getContentElements()->pluck('id')->values()->all());
        FilecabinetPdfTreeHelper::prepare($cabinet);
        $this->assertSame(['b', 'a'], $cabinet->getPdfDossiers()->pluck('id')->all());
        $this->assertSame(['b', 'a'], $cabinet->getContentElements()->pluck('id')->all());
        $this->assertSame(1, $a->sorting_index);
        $this->assertSame(9, $b->sorting_index);
    }

    public function test_pdf_routes_are_separate_and_keep_manager_middleware(): void
    {
        foreach (['forms.pdfOrder', 'forms.storePdfOrder', 'forms.formrows.pdfOrder', 'forms.formrows.storePdfOrder'] as $name) {
            $route = Route::getRoutes()->getByName('ibFilecabinet' . $name);
            $this->assertContains('auth', $route->middleware());
            $this->assertContains('role:fileCabinetManager', $route->middleware());
        }
        $route = Route::getRoutes()->match(Request::create('/filecabinet-manager/forms/pdf-order'));
        $this->assertSame('ibFilecabinetforms.pdfOrder', $route->getName());
        $form = $this->form('form');
        $this->assertSame(route('ibFilecabinetforms.formrows.pdfOrder', ['form' => 'form']), $form->getPdfFormrowsOrderUrl());
        $this->assertSame('IlBronza\\FileCabinet\\Http\\Controllers\\Formrows\\FormrowReorderController@storeMassReorder', Route::getRoutes()->getByName('ibFilecabinetformrows.storeMassReorder')->getActionName());
    }

    public function test_ordering_tables_render_with_their_own_drag_and_drop_endpoints(): void
    {
        foreach ([
            \IlBronza\UikitTemplate\UikitTemplateServiceProvider::class,
            \IlBronza\Buttons\ButtonsServiceProvider::class,
            \IlBronza\FormField\FormFieldServiceProvider::class,
            \IlBronza\Form\FormServiceProvider::class,
            \IlBronza\Datatables\DatatablesServiceProvider::class,
            \IlBronza\CRUD\CRUDServiceProvider::class,
        ] as $provider) {
            $this->app->register($provider);
        }

        $this->form('form');
        $this->form('hidden-form', ['pdf_show' => false]);
        $this->row('row', 'form');
        $this->row('hidden-row', 'form', ['pdf_show' => false]);
        $this->getJson(route('ibFilecabinetforms.pdfOrder'), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()->assertJsonPath('recordsTotal', 1)->assertJsonCount(1, 'data');
        $this->getJson(route('ibFilecabinetforms.formrows.pdfOrder', ['form' => 'form']), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()->assertJsonPath('recordsTotal', 1)->assertJsonCount(1, 'data');
        $this->get(route('ibFilecabinetforms.pdfOrder', ['ibFetcher' => 1]))
            ->assertOk()->assertSee('data-storemasssortingurl="' . route('ibFilecabinetforms.storePdfOrder') . '"', false);
        $this->get(route('ibFilecabinetforms.formrows.pdfOrder', ['form' => 'form', 'ibFetcher' => 1]))
            ->assertOk()->assertSee('data-storemasssortingurl="' . route('ibFilecabinetforms.formrows.storePdfOrder', ['form' => 'form']) . '"', false);
    }
}
