<?php

namespace IlBronza\FileCabinet\Http\Controllers\Offline;

use IlBronza\FileCabinet\Models\Filecabinet;
use IlBronza\FileCabinet\Offline\OfflineAccess;
use IlBronza\FileCabinet\Offline\OfflineAssets;
use IlBronza\FileCabinet\Offline\OfflineQuestionnaire;
use IlBronza\FileCabinet\Offline\OfflineSync;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OfflineController extends Controller
{
    public function shell(OfflineAssets $assets)
    {
        return response()->view('filecabinet::offline.shell', ['assets' => $assets->urls()])->header('Cache-Control', 'no-cache');
    }

    public function worker(OfflineAssets $assets)
    {
        return response($assets->worker(), 200, [
            'Content-Type' => 'application/javascript',
            'Cache-Control' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function entry(string $filecabinet, OfflineQuestionnaire $questionnaire, OfflineAccess $access)
    {
        $questionnaire->dossiers(Filecabinet::getProjectClassName()::findOrFail($filecabinet));
        return redirect()->to(route(config('filecabinet.routePrefix') . 'offline.shell', [
            'cabinet' => $filecabinet, 'owner' => $access->owner(),
        ]));
    }

    public function session(Request $request, OfflineAccess $access)
    {
        return response()->json(['owner' => $access->owner(), 'csrf' => $request->session()->token()])
            ->header('Cache-Control', 'no-store');
    }

    public function snapshot(string $filecabinet, OfflineQuestionnaire $questionnaire)
    {
        return response()->json($questionnaire->snapshot(Filecabinet::getProjectClassName()::findOrFail($filecabinet)))
            ->header('Cache-Control', 'no-store');
    }

    public function sync(Request $request, string $filecabinet, string $dossier, OfflineQuestionnaire $questionnaire, OfflineSync $sync)
    {
        $input = $request->validate([
            'owner' => 'required|string|size:64',
            'operation_id' => 'required|uuid',
            'row_id' => 'required|string|max:255',
            'schema' => 'required|string|size:64',
            'revision' => 'required|string|size:64',
            'value' => 'present',
        ]);
        $dossiers = $questionnaire->dossiers(Filecabinet::getProjectClassName()::findOrFail($filecabinet));
        abort_unless($dossiers->has($dossier), 404);

        return response()->json($sync->apply($dossiers->get($dossier), $input))->header('Cache-Control', 'no-store');
    }
}
