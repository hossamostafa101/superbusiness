<?php

namespace App\Services\App\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantMenuContentSection;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RestaurantMenuContentSectionService
{
    public function create(Workspace $workspace, array $data): RestaurantMenuContentSection
    {
        return DB::transaction(function () use ($workspace, $data) {
            $section = $workspace->restaurantMenuContentSections()->create([
                'branch_id' => $data['branch_id'] ?? null,
                'type' => $data['type'],
                'title' => $data['title'],
                'subtitle' => $data['subtitle'] ?? null,
                'slug' => Str::slug($data['title']) ?: null,

                'background_type' => $data['background_type'] ?? 'solid',
                'background_color' => $data['background_color'] ?? '#ffffff',
                'background_gradient_from' => $data['background_gradient_from'] ?? null,
                'background_gradient_to' => $data['background_gradient_to'] ?? null,
                'text_color' => $data['text_color'] ?? '#111827',
                'button_color' => $data['button_color'] ?? '#2563eb',

                'layout' => $data['layout'] ?? 'horizontal_scroll',
                'is_active' => $data['is_active'] ?? true,
                'sort_order' => $data['sort_order'] ?? 0,

                'starts_at' => $data['starts_at'] ?? null,
                'ends_at' => $data['ends_at'] ?? null,

                'settings' => null,
            ]);

            $this->syncItems($workspace, $section, $data['item_ids'] ?? []);

            return $section;
        });
    }

    public function update(Workspace $workspace, RestaurantMenuContentSection $section, array $data): RestaurantMenuContentSection
    {
        return DB::transaction(function () use ($workspace, $section, $data) {
            $section->update([
                'branch_id' => $data['branch_id'] ?? null,
                'type' => $data['type'],
                'title' => $data['title'],
                'subtitle' => $data['subtitle'] ?? null,
                'slug' => Str::slug($data['title']) ?: null,

                'background_type' => $data['background_type'] ?? 'solid',
                'background_color' => $data['background_color'] ?? '#ffffff',
                'background_gradient_from' => $data['background_gradient_from'] ?? null,
                'background_gradient_to' => $data['background_gradient_to'] ?? null,
                'text_color' => $data['text_color'] ?? '#111827',
                'button_color' => $data['button_color'] ?? '#2563eb',

                'layout' => $data['layout'] ?? 'horizontal_scroll',
                'is_active' => $data['is_active'] ?? false,
                'sort_order' => $data['sort_order'] ?? 0,

                'starts_at' => $data['starts_at'] ?? null,
                'ends_at' => $data['ends_at'] ?? null,
            ]);

            $this->syncItems($workspace, $section, $data['item_ids'] ?? []);

            return $section;
        });
    }

    public function delete(RestaurantMenuContentSection $section): void
    {
        $section->delete();
    }

    private function syncItems(Workspace $workspace, RestaurantMenuContentSection $section, array $itemIds): void
    {
        if (! in_array($section->type, ['featured_items', 'item_collection'], true)) {
            $section->sectionItems()->delete();
            return;
        }

        $itemIds = collect($itemIds)
            ->filter()
            ->unique()
            ->values();

        $section->sectionItems()->delete();

        foreach ($itemIds as $index => $itemId) {
            $section->sectionItems()->create([
                'workspace_id' => $workspace->id,
                'branch_id' => $section->branch_id,
                'item_id' => $itemId,
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }
    }
}