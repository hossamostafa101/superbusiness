<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\RestaurantMenu\StoreRestaurantMenuContentSectionRequest;
use App\Http\Requests\App\RestaurantMenu\UpdateRestaurantMenuContentSectionRequest;
use App\Models\RestaurantMenu\RestaurantMenuContentSection;
use App\Models\Workspace;
use App\Services\App\RestaurantMenu\RestaurantMenuContentSectionService;
use App\Services\Core\FeatureLimitService;
use Illuminate\Http\Request;

class RestaurantMenuContentSectionController extends Controller
{
    public function __construct(
        private readonly RestaurantMenuContentSectionService $contentSectionService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function index(Request $request, Workspace $workspace)
    {
        if (! $this->featureLimitService->enabled($workspace, 'restaurant_menu_content_sections_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.settings.edit', $workspace)
                ->with('error', 'أقسام محتوى المنيو غير متاحة في باقتك الحالية.');
        }

        $sections = $workspace->restaurantMenuContentSections()
            ->with('branch:id,name')
            ->withCount(['sectionItems', 'offers'])
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->when($request->filled('branch_id'), function ($query) use ($request) {
                $query->where('branch_id', $request->input('branch_id'));
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        $branches = $workspace->restaurantBranches()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('app.restaurant-menu.content-sections.index', compact(
            'workspace',
            'sections',
            'branches'
        ));
    }

    public function create(Workspace $workspace)
    {
        if (! $this->featureLimitService->enabled($workspace, 'restaurant_menu_content_sections_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.settings.edit', $workspace)
                ->with('error', 'أقسام محتوى المنيو غير متاحة في باقتك الحالية.');
        }

        $currentCount = $workspace->restaurantMenuContentSections()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_menu_content_sections_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.content-sections.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى لأقسام المحتوى في باقتك الحالية.');
        }

        [$branches, $items] = $this->formData($workspace);

        $previewUrl = $this->previewUrl($workspace);

        $customBgEnabled = $this->featureLimitService->enabled(
            $workspace,
            'restaurant_menu_section_custom_bg_enabled'
        );

        return view('app.restaurant-menu.content-sections.create', compact(
            'workspace',
            'branches',
            'items',
            'customBgEnabled',
            'previewUrl'
        ));
    }

    public function store(StoreRestaurantMenuContentSectionRequest $request, Workspace $workspace)
    {
        if (! $this->featureLimitService->enabled($workspace, 'restaurant_menu_content_sections_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.settings.edit', $workspace)
                ->with('error', 'أقسام محتوى المنيو غير متاحة في باقتك الحالية.');
        }

        $currentCount = $workspace->restaurantMenuContentSections()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_menu_content_sections_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.content-sections.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى لأقسام المحتوى في باقتك الحالية.');
        }

        $data = $request->validated();

        if (! $this->featureLimitService->enabled($workspace, 'restaurant_menu_section_custom_bg_enabled')) {
            $data = $this->forceDefaultBackground($data);
        }

        if (
            $data['type'] === 'offers_slider'
            && ! $this->featureLimitService->enabled($workspace, 'restaurant_menu_offers_slider_enabled')
        ) {
            return back()->withInput()->with('error', 'سلايدر العروض غير متاح في باقتك الحالية.');
        }

        $section = $this->contentSectionService->create($workspace, $data);

        if ($section->type === 'offers_slider') {
            return redirect()
                ->route('app.restaurant-menu.content-sections.offers.index', [$workspace, $section])
                ->with('success', 'تم إنشاء القسم. أضف العروض الآن.');
        }

        return redirect()
            ->route('app.restaurant-menu.content-sections.index', $workspace)
            ->with('success', 'تم إنشاء القسم بنجاح.');
    }

    public function edit(Workspace $workspace, RestaurantMenuContentSection $contentSection)
    {
        $this->ensureBelongsToWorkspace($workspace, $contentSection);

        [$branches, $items] = $this->formData($workspace);

        $previewUrl = $this->previewUrl($workspace);

        $contentSection->load('sectionItems');

        $selectedItemIds = $contentSection->sectionItems()
            ->orderBy('sort_order')
            ->pluck('item_id')
            ->toArray();

        $customBgEnabled = $this->featureLimitService->enabled(
            $workspace,
            'restaurant_menu_section_custom_bg_enabled'
        );

        return view('app.restaurant-menu.content-sections.edit', compact(
            'workspace',
            'contentSection',
            'branches',
            'items',
            'selectedItemIds',
            'customBgEnabled',
            'previewUrl'
        ));
    }

    public function update(UpdateRestaurantMenuContentSectionRequest $request, Workspace $workspace, RestaurantMenuContentSection $contentSection)
    {
        $this->ensureBelongsToWorkspace($workspace, $contentSection);

        $data = $request->validated();

        if (! $this->featureLimitService->enabled($workspace, 'restaurant_menu_section_custom_bg_enabled')) {
            $data = $this->forceDefaultBackground($data);
        }

        if (
            $data['type'] === 'offers_slider'
            && ! $this->featureLimitService->enabled($workspace, 'restaurant_menu_offers_slider_enabled')
        ) {
            return back()->withInput()->with('error', 'سلايدر العروض غير متاح في باقتك الحالية.');
        }

        $this->contentSectionService->update($workspace, $contentSection, $data);

        return redirect()
            ->route('app.restaurant-menu.content-sections.index', $workspace)
            ->with('success', 'تم تحديث القسم بنجاح.');
    }

    public function destroy(Workspace $workspace, RestaurantMenuContentSection $contentSection)
    {
        $this->ensureBelongsToWorkspace($workspace, $contentSection);

        $this->contentSectionService->delete($contentSection);

        return redirect()
            ->route('app.restaurant-menu.content-sections.index', $workspace)
            ->with('success', 'تم حذف القسم بنجاح.');
    }

    private function formData(Workspace $workspace): array
    {
        $branches = $workspace->restaurantBranches()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        $items = $workspace->restaurantMenuItems()
            ->with('branch:id,name')
            ->where('is_available', true)
            ->orderBy('name')
            ->get(['id', 'branch_id', 'name', 'price', 'sale_price', 'currency']);

        return [$branches, $items];
    }

    private function forceDefaultBackground(array $data): array
    {
        $data['background_type'] = 'solid';
        $data['background_color'] = '#ffffff';
        $data['background_gradient_from'] = null;
        $data['background_gradient_to'] = null;
        $data['text_color'] = '#111827';
        $data['button_color'] = '#2563eb';

        return $data;
    }

    private function ensureBelongsToWorkspace(Workspace $workspace, RestaurantMenuContentSection $contentSection): void
    {
        abort_if((int) $contentSection->workspace_id !== (int) $workspace->id, 404);
    }

    














    private function previewUrl(Workspace $workspace): string
{
    $branch = $workspace->restaurantBranches()
        ->where('is_active', true)
        ->orderByDesc('is_default')
        ->orderBy('sort_order')
        ->orderBy('id')
        ->first();

    return $branch
        ? route('public.restaurant-menu.branch', [$workspace, $branch])
        : route('public.restaurant-menu.workspace', $workspace);
}
}