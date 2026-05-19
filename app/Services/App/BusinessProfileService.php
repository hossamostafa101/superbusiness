<?php

namespace App\Services\App;

use App\Models\BusinessProfile;
use App\Models\Workspace;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BusinessProfileService
{
    public function update(
        Workspace $workspace,
        array $data,
        ?UploadedFile $logo = null,
        ?UploadedFile $coverImage = null
    ): BusinessProfile {
        return DB::transaction(function () use ($workspace, $data, $logo, $coverImage) {
            $profile = $workspace->businessProfile ?: new BusinessProfile([
                'workspace_id' => $workspace->id,
            ]);

            if ($logo) {
                if ($profile->logo) {
                    Storage::disk('public')->delete($profile->logo);
                }

                $data['logo'] = $logo->store('business/logos', 'public');
            }

            if ($coverImage) {
                if ($profile->cover_image) {
                    Storage::disk('public')->delete($profile->cover_image);
                }

                $data['cover_image'] = $coverImage->store('business/covers', 'public');
            }

            $profile->fill([
                'display_name' => $data['display_name'],
                'tagline' => $data['tagline'] ?? null,
                'description' => $data['description'] ?? null,

                'logo' => $data['logo'] ?? $profile->logo,
                'cover_image' => $data['cover_image'] ?? $profile->cover_image,

                'whatsapp_number' => $data['whatsapp_number'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,

                'address' => $data['address'] ?? null,
                'location_url' => $data['location_url'] ?? null,

                'theme_color' => $data['theme_color'] ?? '#111827',
                'button_color' => $data['button_color'] ?? '#2563eb',
                'text_color' => $data['text_color'] ?? '#111827',

                'is_published' => $data['is_published'] ?? false,
            ]);

            $profile->save();

            return $profile;
        });
    }
}