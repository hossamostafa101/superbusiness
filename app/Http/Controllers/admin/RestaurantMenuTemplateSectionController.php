<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RestaurantMenu\StoreRestaurantMenuTemplateSectionRequest;
use App\Http\Requests\Admin\RestaurantMenu\UpdateRestaurantMenuTemplateSectionRequest;
use App\Models\RestaurantMenu\RestaurantMenuTemplateSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RestaurantMenuTemplateSectionController extends Controller
{
    public function index(Request $request)
    {
        $sections = RestaurantMenuTemplateSection::query()
            ->when($request->filled('section_type'), function ($query) use ($request) {
                $query->where('section_type', $request->input('section_type'));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('key', 'like', "%{$search}%");
                });
            })
            ->orderBy('section_type')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.restaurant-menu.sections.index', compact('sections'));
    }

    public function create()
    {
        return view('admin.restaurant-menu.sections.create', [
            'sectionTypes' => $this->sectionTypes(),
        ]);
    }

    public function store(StoreRestaurantMenuTemplateSectionRequest $request)
    {
        $data = $request->validated();

        $previewImage = null;

        if ($request->hasFile('preview_image')) {
            $previewImage = $request->file('preview_image')
                ->store('restaurant-menu/sections', 'public');
        }

        RestaurantMenuTemplateSection::create([
            'section_type' => $data['section_type'],
            'name' => $data['name'],
            'key' => $data['key'],
            'description' => $data['description'] ?? null,
            'preview_image' => $previewImage,
            'config' => [
                'view' => $data['view'],
            ],
            'is_premium' => $data['is_premium'] ?? false,
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()
            ->route('admin.restaurant-menu-template-sections.index')
            ->with('success', 'تم إنشاء القسم بنجاح.');
    }

    public function edit(RestaurantMenuTemplateSection $restaurantMenuTemplateSection)
    {
        return view('admin.restaurant-menu.sections.edit', [
            'restaurantMenuTemplateSection' => $restaurantMenuTemplateSection,
            'sectionTypes' => $this->sectionTypes(),
        ]);
    }

    public function update(
        UpdateRestaurantMenuTemplateSectionRequest $request,
        RestaurantMenuTemplateSection $restaurantMenuTemplateSection
    ) {
        $data = $request->validated();

        $previewImage = $restaurantMenuTemplateSection->preview_image;

        if ($request->hasFile('preview_image')) {
            if ($previewImage && ! str_starts_with($previewImage, 'images/')) {
                Storage::disk('public')->delete($previewImage);
            }

            $previewImage = $request->file('preview_image')
                ->store('restaurant-menu/sections', 'public');
        }

        $restaurantMenuTemplateSection->update([
            'section_type' => $data['section_type'],
            'name' => $data['name'],
            'key' => $data['key'],
            'description' => $data['description'] ?? null,
            'preview_image' => $previewImage,
            'config' => [
                'view' => $data['view'],
            ],
            'is_premium' => $data['is_premium'] ?? false,
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()
            ->route('admin.restaurant-menu-template-sections.index')
            ->with('success', 'تم تحديث القسم بنجاح.');
    }

    public function destroy(RestaurantMenuTemplateSection $restaurantMenuTemplateSection)
    {
        if ($restaurantMenuTemplateSection->preview_image && ! str_starts_with($restaurantMenuTemplateSection->preview_image, 'images/')) {
            Storage::disk('public')->delete($restaurantMenuTemplateSection->preview_image);
        }

        $restaurantMenuTemplateSection->delete();

        return back()->with('success', 'تم حذف القسم بنجاح.');
    }

    private function sectionTypes(): array
    {
        return [
            'hero' => 'Hero',
            'branch_switch' => 'Branch Switch',
            'category_tabs' => 'Category Tabs',
            'items' => 'Items',
            'item_modal' => 'Item Modal',
            'cart' => 'Cart',
            'invoice' => 'Invoice / Session',
            'footer' => 'Footer',
        ];
    }
}