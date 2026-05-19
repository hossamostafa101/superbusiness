<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RestaurantMenu\StoreRestaurantMenuTemplateRequest;
use App\Http\Requests\Admin\RestaurantMenu\UpdateRestaurantMenuTemplateRequest;
use App\Models\RestaurantMenu\RestaurantMenuTemplate;
use App\Models\RestaurantMenu\RestaurantMenuTemplateSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RestaurantMenuTemplateController extends Controller
{
    public function index(Request $request)
    {
        $templates = RestaurantMenuTemplate::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('key', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.restaurant-menu.templates.index', compact('templates'));
    }

    public function create()
    {
        $sectionOptions = $this->sectionOptions();

        return view('admin.restaurant-menu.templates.create', compact('sectionOptions'));
    }

    public function store(StoreRestaurantMenuTemplateRequest $request)
    {
        $data = $request->validated();

        $this->validateLayoutSections($data);

        $previewImage = null;

        if ($request->hasFile('preview_image')) {
            $previewImage = $request->file('preview_image')
                ->store('restaurant-menu/templates', 'public');
        }

        RestaurantMenuTemplate::create([
            'name' => $data['name'],
            'key' => $data['key'],
            'description' => $data['description'] ?? null,
            'preview_image' => $previewImage,
            'layout_config' => $this->layoutConfig($data),
            'is_premium' => $data['is_premium'] ?? false,
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()
            ->route('admin.restaurant-menu-templates.index')
            ->with('success', 'تم إنشاء القالب بنجاح.');
    }

    public function edit(RestaurantMenuTemplate $restaurantMenuTemplate)
    {
        $sectionOptions = $this->sectionOptions();

        return view('admin.restaurant-menu.templates.edit', compact(
            'restaurantMenuTemplate',
            'sectionOptions'
        ));
    }

    public function update(UpdateRestaurantMenuTemplateRequest $request, RestaurantMenuTemplate $restaurantMenuTemplate)
    {
        $data = $request->validated();

        $this->validateLayoutSections($data);
        
        $previewImage = $restaurantMenuTemplate->preview_image;

        if ($request->hasFile('preview_image')) {
            if ($previewImage && ! str_starts_with($previewImage, 'images/')) {
                Storage::disk('public')->delete($previewImage);
            }

            $previewImage = $request->file('preview_image')
                ->store('restaurant-menu/templates', 'public');
        }

        $restaurantMenuTemplate->update([
            'name' => $data['name'],
            'key' => $data['key'],
            'description' => $data['description'] ?? null,
            'preview_image' => $previewImage,
            'layout_config' => $this->layoutConfig($data),
            'is_premium' => $data['is_premium'] ?? false,
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()
            ->route('admin.restaurant-menu-templates.index')
            ->with('success', 'تم تحديث القالب بنجاح.');
    }

    public function destroy(RestaurantMenuTemplate $restaurantMenuTemplate)
    {
        if ($restaurantMenuTemplate->assignments()->exists()) {
            return back()->with('error', 'لا يمكن حذف قالب مستخدم حاليًا.');
        }

        if ($restaurantMenuTemplate->preview_image && ! str_starts_with($restaurantMenuTemplate->preview_image, 'images/')) {
            Storage::disk('public')->delete($restaurantMenuTemplate->preview_image);
        }

        $restaurantMenuTemplate->delete();

        return back()->with('success', 'تم حذف القالب بنجاح.');
    }

    private function layoutConfig(array $data): array
    {
        return [
            'hero' => $data['hero'],
            'branch_switch' => $data['branch_switch'],
            'category_tabs' => $data['category_tabs'],
            'items' => $data['items'],
            'item_modal' => $data['item_modal'],
            'cart' => $data['cart'],
            'invoice' => $data['invoice'],
            'footer' => $data['footer'],
        ];
    }

  private function sectionOptions()
{
    return RestaurantMenuTemplateSection::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get()
        ->groupBy('section_type');
}




private function validateLayoutSections(array $data): void
{
    $expected = [
        'hero' => 'hero',
        'branch_switch' => 'branch_switch',
        'category_tabs' => 'category_tabs',
        'items' => 'items',
        'item_modal' => 'item_modal',
        'cart' => 'cart',
        'invoice' => 'invoice',
        'footer' => 'footer',
    ];

    foreach ($expected as $field => $sectionType) {
        $exists = RestaurantMenuTemplateSection::query()
            ->where('key', $data[$field])
            ->where('section_type', $sectionType)
            ->where('is_active', true)
            ->exists();

        if (! $exists) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                $field => 'القسم المحدد غير صحيح أو غير نشط.',
            ]);
        }
    }
}
}