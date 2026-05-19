<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\UpdateBusinessProfileRequest;
use App\Models\Workspace;
use App\Services\App\BusinessProfileService;

class BusinessProfileController extends Controller
{
    public function __construct(
        private readonly BusinessProfileService $businessProfileService
    ) {}

    public function edit(Workspace $workspace)
    {
        $workspace->load('businessProfile');

        $profile = $workspace->businessProfile;

        return view('app.business-profile.edit', compact('workspace', 'profile'));
    }

  public function update(UpdateBusinessProfileRequest $request, Workspace $workspace)
{
    $this->businessProfileService->update(
        workspace: $workspace,
        data: $request->validated(),
        logo: $request->file('logo'),
        coverImage: $request->file('cover_image')
    );

    return redirect()
        ->route('app.business-profile.edit', $workspace)
        ->with('success', 'تم تحديث بيانات الصفحة بنجاح.');
}
}