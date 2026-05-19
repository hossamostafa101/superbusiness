<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\RestaurantMenu\StoreRestaurantMenuOfferRequest;
use App\Http\Requests\App\RestaurantMenu\UpdateRestaurantMenuOfferRequest;
use App\Models\RestaurantMenu\RestaurantMenuContentSection;
use App\Models\RestaurantMenu\RestaurantMenuOffer;
use App\Models\Workspace;
use Illuminate\Support\Facades\Storage;

class RestaurantMenuOfferController extends Controller
{
    public function index(Workspace $workspace, RestaurantMenuContentSection $contentSection)
    {
        $this->ensureSection($workspace, $contentSection);

        $offers = $contentSection->offers()
            ->with('item:id,name')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20);

        return view('app.restaurant-menu.content-sections.offers.index', compact(
            'workspace',
            'contentSection',
            'offers'
        ));
    }

    public function create(Workspace $workspace, RestaurantMenuContentSection $contentSection)
    {
        $this->ensureSection($workspace, $contentSection);

        $items = $this->items($workspace);

        return view('app.restaurant-menu.content-sections.offers.create', compact(
            'workspace',
            'contentSection',
            'items'
        ));
    }

    public function store(StoreRestaurantMenuOfferRequest $request, Workspace $workspace, RestaurantMenuContentSection $contentSection)
    {
        $this->ensureSection($workspace, $contentSection);

        $data = $request->validated();

        $image = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image')
                ->store('restaurant-menu/offers', 'public');
        }

        $contentSection->offers()->create([
            'workspace_id' => $workspace->id,
            'branch_id' => $contentSection->branch_id,
            'item_id' => $data['item_id'] ?? null,

            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'description' => $data['description'] ?? null,

            'image' => $image,

            'badge_text' => $data['badge_text'] ?? null,

            'old_price' => $data['old_price'] ?? null,
            'new_price' => $data['new_price'] ?? null,
            'currency' => $data['currency'] ?? 'EGP',

            'button_text' => $data['button_text'] ?? null,
            'button_url' => $data['button_url'] ?? null,

            'background_color' => $data['background_color'] ?? '#111827',
            'text_color' => $data['text_color'] ?? '#ffffff',

            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,

            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
        ]);

        return redirect()
            ->route('app.restaurant-menu.content-sections.offers.index', [$workspace, $contentSection])
            ->with('success', 'تم إنشاء العرض بنجاح.');
    }

    public function edit(Workspace $workspace, RestaurantMenuContentSection $contentSection, RestaurantMenuOffer $offer)
    {
        $this->ensureSection($workspace, $contentSection);
        $this->ensureOffer($contentSection, $offer);

        $items = $this->items($workspace);

        return view('app.restaurant-menu.content-sections.offers.edit', compact(
            'workspace',
            'contentSection',
            'offer',
            'items'
        ));
    }

    public function update(
        UpdateRestaurantMenuOfferRequest $request,
        Workspace $workspace,
        RestaurantMenuContentSection $contentSection,
        RestaurantMenuOffer $offer
    ) {
        $this->ensureSection($workspace, $contentSection);
        $this->ensureOffer($contentSection, $offer);

        $data = $request->validated();

        $image = $offer->image;

        if ($request->hasFile('image')) {
            if ($image && ! str_starts_with($image, 'images/')) {
                Storage::disk('public')->delete($image);
            }

            $image = $request->file('image')
                ->store('restaurant-menu/offers', 'public');
        }

        $offer->update([
            'item_id' => $data['item_id'] ?? null,

            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'description' => $data['description'] ?? null,

            'image' => $image,

            'badge_text' => $data['badge_text'] ?? null,

            'old_price' => $data['old_price'] ?? null,
            'new_price' => $data['new_price'] ?? null,
            'currency' => $data['currency'] ?? 'EGP',

            'button_text' => $data['button_text'] ?? null,
            'button_url' => $data['button_url'] ?? null,

            'background_color' => $data['background_color'] ?? '#111827',
            'text_color' => $data['text_color'] ?? '#ffffff',

            'is_active' => $data['is_active'] ?? false,
            'sort_order' => $data['sort_order'] ?? 0,

            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
        ]);

        return redirect()
            ->route('app.restaurant-menu.content-sections.offers.index', [$workspace, $contentSection])
            ->with('success', 'تم تحديث العرض بنجاح.');
    }

    public function destroy(Workspace $workspace, RestaurantMenuContentSection $contentSection, RestaurantMenuOffer $offer)
    {
        $this->ensureSection($workspace, $contentSection);
        $this->ensureOffer($contentSection, $offer);

        if ($offer->image && ! str_starts_with($offer->image, 'images/')) {
            Storage::disk('public')->delete($offer->image);
        }

        $offer->delete();

        return redirect()
            ->route('app.restaurant-menu.content-sections.offers.index', [$workspace, $contentSection])
            ->with('success', 'تم حذف العرض بنجاح.');
    }

    private function items(Workspace $workspace)
    {
        return $workspace->restaurantMenuItems()
            ->where('is_available', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'sale_price', 'currency']);
    }

    private function ensureSection(Workspace $workspace, RestaurantMenuContentSection $contentSection): void
    {
        abort_if((int) $contentSection->workspace_id !== (int) $workspace->id, 404);
        abort_if($contentSection->type !== 'offers_slider', 404);
    }

    private function ensureOffer(RestaurantMenuContentSection $contentSection, RestaurantMenuOffer $offer): void
    {
        abort_if((int) $offer->section_id !== (int) $contentSection->id, 404);
    }
}