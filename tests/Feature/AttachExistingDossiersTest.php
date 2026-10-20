<?php

namespace IlBronza\FileCabinet\Tests\Feature;

use IlBronza\FileCabinet\Helpers\AttachExistingDossiersHelper;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\DossierFilecabinet;
use IlBronza\FileCabinet\Models\Filecabinet;
use IlBronza\FileCabinet\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class AttachExistingDossiersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        foreach (['dossier' => Dossier::class, 'filecabinet' => Filecabinet::class, 'dossierFilecabinet' => DossierFilecabinet::class] as $name => $class) {
            config()->set('filecabinet.models.'.$name, ['class' => $class, 'table' => $name]);
        }
        foreach (['dossier' => ['dossierable_type', 'dossierable_id'], 'filecabinet' => ['filecabinetable_type', 'filecabinetable_id'], 'dossierFilecabinet' => ['filecabinet_id', 'dossier_id']] as $name => $columns) {
            Schema::create($name, function (Blueprint $table) use ($columns) {
                $table->string('id')->primary();
                foreach ($columns as $column) $table->string($column)->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
        DB::table('filecabinet')->insert(['id' => 'cabinet', 'filecabinetable_type' => 'client', 'filecabinetable_id' => '1']);
        foreach (['first' => '1', 'second' => '1', 'foreign' => '2'] as $id => $owner) {
            DB::table('dossier')->insert(['id' => $id, 'dossierable_type' => 'client', 'dossierable_id' => $owner]);
        }
    }

    public function test_all_instances_are_attached_once_and_soft_deleted_pivots_are_restored(): void
    {
        $cabinet = Filecabinet::findOrFail('cabinet');
        $dossiers = Dossier::where('dossierable_id', '1')->get();
        $snapshot = DB::table('dossier')->get()->toJson();
        $this->assertSame(2, AttachExistingDossiersHelper::attach($cabinet, $dossiers));
        $this->assertSame(0, AttachExistingDossiersHelper::attach($cabinet, $dossiers));
        DB::table('dossierFilecabinet')->where('dossier_id', 'first')->update(['deleted_at' => now()]);
        $this->assertSame(1, AttachExistingDossiersHelper::attach($cabinet, $dossiers));
        $this->assertSame(2, DB::table('dossierFilecabinet')->whereNull('deleted_at')->count());
        $this->assertSame($snapshot, DB::table('dossier')->get()->toJson());
    }

    public function test_mixed_owners_are_rejected_without_partial_attachments(): void
    {
        try {
            AttachExistingDossiersHelper::attach(Filecabinet::findOrFail('cabinet'), Dossier::all());
            $this->fail('Foreign dossiers must be rejected.');
        } catch (InvalidArgumentException) {
            $this->assertSame(0, DB::table('dossierFilecabinet')->count());
        }
    }

    public function test_archived_dossiers_are_rejected(): void
    {
        DB::table('dossier')->where('id', 'first')->update(['deleted_at' => now()]);
        $this->expectException(InvalidArgumentException::class);
        AttachExistingDossiersHelper::attach(Filecabinet::findOrFail('cabinet'), Dossier::withTrashed()->where('id', 'first')->get());
    }
}
