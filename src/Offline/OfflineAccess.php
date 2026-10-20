<?php

namespace IlBronza\FileCabinet\Offline;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class OfflineAccess
{
    public function owner() : string
    {
        abort_unless(Auth::check(), 401);

        return hash('sha256', get_class(Auth::user()) . ':' . Auth::id());
    }

    public function authorize(Model $model) : void
    {
        abort_unless(Auth::check(), 401);
        $hasModelPermissions = method_exists($model, 'userCanSee') && method_exists($model, 'userCanUpdate');
        $policy = Gate::getPolicyFor($model);
        $authorizer = config('filecabinet.offline.authorizer');
        abort_unless($hasModelPermissions || $policy || $authorizer, 403);
        if ($hasModelPermissions)
            abort_unless($model->userCanSee() && $model->userCanUpdate(), 403);

        // Respect application policies in addition to the package's model permissions.
        if ($policy)
            Gate::authorize('update', $model);

        if ($authorizer)
            abort_unless(app()->call($authorizer, ['model' => $model, 'user' => Auth::user()]), 403);
    }
}
