<?php

namespace App\Services\App;

use App\Models\BusinessLink;
use App\Models\Workspace;

class BusinessLinkService
{
    public function create(Workspace $workspace, array $data): BusinessLink
    {
        return $workspace->businessLinks()->create([
            'title' => $data['title'],
            'url' => $data['url'],
            'icon' => $data['icon'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update(BusinessLink $businessLink, array $data): BusinessLink
    {
        $businessLink->update([
            'title' => $data['title'],
            'url' => $data['url'],
            'icon' => $data['icon'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? false,
        ]);

        return $businessLink;
    }

    public function delete(BusinessLink $businessLink): void
    {
        $businessLink->delete();
    }
}