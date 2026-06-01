<?php

namespace Modules\Medical\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Workspace;

class MedicalPublicController extends Controller
{
    public function show(Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        return view('medical::public.show', compact('workspace'));
    }
}