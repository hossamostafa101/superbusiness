<?php

namespace App\Http\Controllers\Public\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Models\RestaurantMenu\RestaurantBranch;
use App\Models\RestaurantMenu\RestaurantInvoice;
use App\Models\RestaurantMenu\RestaurantMenuContentSection;
use App\Models\RestaurantMenu\RestaurantMenuContentSectionItem;
use App\Models\RestaurantMenu\RestaurantMenuOffer;
use App\Models\Workspace;
use App\Services\Core\FeatureLimitService;
use App\Services\Public\RestaurantMenu\RestaurantInvoiceSessionService;
use App\Services\Public\RestaurantMenu\RestaurantMenuSettingReader;
use App\Services\Public\RestaurantMenu\RestaurantMenuThemeResolver;
use Illuminate\Support\Collection;

class PublicRestaurantMenuController extends Controller
{

    public function __construct(
        private readonly RestaurantMenuSettingReader $settingReader,
        private readonly RestaurantInvoiceSessionService $invoiceSessionService,
        private readonly FeatureLimitService $featureLimitService,
        private readonly RestaurantMenuThemeResolver $themeResolver
    ) {}


    public function showWorkspace(Workspace $workspace)
    {
        abort_if($workspace->status !== 'active', 404);

        $branch = $workspace->restaurantBranches()
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();

        if (! $branch) {
            $branch = $workspace->restaurantBranches()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();
        }

        abort_if(! $branch, 404);

        return redirect()->route('public.restaurant-menu.branch', [$workspace, $branch]);
    }


    public function showBranch(Workspace $workspace, RestaurantBranch $branch)
    {
        abort_if($workspace->status !== 'active', 404);
        abort_if((int) $branch->workspace_id !== (int) $workspace->id, 404);
        abort_if(! $branch->is_active, 404);

        $workspace->loadMissing('businessProfile');

        $selectedTable = null;

        if (request()->filled('table_code')) {
            $selectedTable = $workspace->restaurantTables()
                ->where('branch_id', $branch->id)
                ->where('code', request()->input('table_code'))
                ->where('is_active', true)
                ->first();
        }

        $orderingMode = $this->settingReader->orderingMode($workspace);

        $openInvoiceEnabled =
            $orderingMode === 'open_invoice'
            && $this->featureLimitService->enabled($workspace, 'restaurant_open_invoice_enabled');

        $openInvoice = null;
        $currentInvoice = null;
        $currentInvoiceGuest = null;

        if ($openInvoiceEnabled && $selectedTable) {
            $openInvoice = $this->invoiceSessionService->findOpenInvoiceForTable(
                workspace: $workspace,
                branch: $branch,
                table: $selectedTable
            );
        }

        if (request()->filled('invoice_id')) {
            $currentInvoice = RestaurantInvoice::query()
                ->where('workspace_id', $workspace->id)
                ->where('branch_id', $branch->id)
                ->where('status', 'open')
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->find(request()->integer('invoice_id'));

            if ($currentInvoice) {
                $currentInvoiceGuest = $this->invoiceSessionService->currentGuest($currentInvoice);

                if (! $currentInvoiceGuest) {
                    $currentInvoice = null;
                }
            }




            if ($currentInvoice && ! $selectedTable && $currentInvoice->table_id) {
                $selectedTable = $workspace->restaurantTables()
                    ->where('branch_id', $branch->id)
                    ->where('id', $currentInvoice->table_id)
                    ->where('is_active', true)
                    ->first();
            }
        }

        $invoiceJoinPolicy = $this->settingReader->joinPolicy($workspace);
        $allowNewInvoiceWhenTableBusy = $this->settingReader->allowNewInvoiceWhenTableBusy($workspace);

        $branches = $workspace->restaurantBranches()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'name', 'slug', 'is_default']);

        $branch->load([
            'categories' => function ($query) {
                $query->where('is_active', true)
                    ->with([
                        'items' => function ($itemQuery) {
                            $itemQuery->where('is_available', true)
                                ->with([
                                    'activeVariants',
                                    'activeOptionGroups',
                                ])
                                ->orderByDesc('is_featured')
                                ->orderBy('sort_order')
                                ->orderBy('id');
                        },
                    ])
                    ->orderBy('sort_order')
                    ->orderBy('id');
            },
        ]);


        $menuTheme = $this->themeResolver->resolve($workspace);


        $contentSections = $workspace->restaurantMenuContentSections()
            ->where(function ($query) use ($branch) {
                $query->whereNull('branch_id')
                    ->orWhere('branch_id', $branch->id);
            })
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->with([
                'activeSectionItems.item' => function ($query) {
                    $query->where('is_available', true)
                        ->with(['activeVariants', 'activeOptionGroups']);
                },
                // 'activeOffers.item',

                'activeOffers.item' => function ($query) {
                    $query->select('id', 'name', 'image', 'price', 'sale_price', 'currency');
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();



        if (request()->boolean('content_section_preview')) {
            $previewSection = $this->makePreviewContentSection($workspace, $branch);

            if ($previewSection) {
                $contentSections = collect([$previewSection])
                    ->merge($contentSections)
                    ->values();
            }
        }


        $links = $workspace->businessLinks()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();


        return view('public.restaurant-menu.show', compact(
            'workspace',
            'branch',
            'branches',
            'selectedTable',
            'orderingMode',
            'openInvoiceEnabled',
            'openInvoice',
            'currentInvoice',
            'currentInvoiceGuest',
            'invoiceJoinPolicy',
            'allowNewInvoiceWhenTableBusy',
            'menuTheme',
            'contentSections',
            'links',
        ));
    }

    public function showBranchX(Workspace $workspace, RestaurantBranch $branch)
    {
        abort_if($workspace->status !== 'active', 404);
        abort_if((int) $branch->workspace_id !== (int) $workspace->id, 404);
        abort_if(! $branch->is_active, 404);

        $workspace->loadMissing('businessProfile');

        $selectedTable = null;

        if (request()->filled('table_code')) {
            $selectedTable = $workspace->restaurantTables()
                ->where('branch_id', $branch->id)
                ->where('code', request()->input('table_code'))
                ->where('is_active', true)
                ->first();
        }

        $branches = $workspace->restaurantBranches()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'name', 'slug', 'is_default']);

        $branch->load([
            'categories' => function ($query) {
                $query->where('is_active', true)
                    ->with([
                        'items' => function ($itemQuery) {
                            $itemQuery->where('is_available', true)
                                ->with([
                                    'activeVariants',
                                    'activeOptionGroups',
                                ])
                                ->orderByDesc('is_featured')
                                ->orderBy('sort_order')
                                ->orderBy('id');
                        },
                    ])
                    ->orderBy('sort_order')
                    ->orderBy('id');
            },
        ]);


        return view('public.restaurant-menu.show', compact(
            'workspace',
            'branch',
            'branches',
            'selectedTable',
        ));
    }
    public function showBranchXX(Workspace $workspace, RestaurantBranch $branch)
    {
        abort_if($workspace->status !== 'active', 404);
        abort_if((int) $branch->workspace_id !== (int) $workspace->id, 404);
        abort_if(! $branch->is_active, 404);

        $workspace->loadMissing('businessProfile');

        $branches = $workspace->restaurantBranches()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'name', 'slug', 'is_default']);

        $branch->load([
            'categories' => function ($query) {
                $query->where('is_active', true)
                    ->with([
                        'items' => function ($itemQuery) {
                            $itemQuery->where('is_available', true)
                                ->with([
                                    'activeVariants',
                                    'activeOptionGroups',
                                ])
                                ->orderByDesc('is_featured')
                                ->orderBy('sort_order')
                                ->orderBy('id');
                        },
                    ])
                    ->orderBy('sort_order')
                    ->orderBy('id');
            },
        ]);

        return view('public.restaurant-menu.show', compact(
            'workspace',
            'branch',
            'branches'
        ));
    }














    private function makePreviewContentSection(Workspace $workspace, RestaurantBranch $branch): ?RestaurantMenuContentSection
    {
        $type = request()->input('preview_section_type', 'featured_items');

        if (! in_array($type, ['featured_items', 'item_collection', 'offers_slider'], true)) {
            return null;
        }

        $section = new RestaurantMenuContentSection([
            'workspace_id' => $workspace->id,
            'branch_id' => request()->integer('preview_branch_id') ?: null,
            'type' => $type,
            'title' => request()->input('preview_title', 'معاينة القسم'),
            'subtitle' => request()->input('preview_subtitle'),
            'background_type' => request()->input('preview_background_type', 'solid'),
            'background_color' => request()->input('preview_background_color', '#ffffff'),
            'background_gradient_from' => request()->input('preview_background_gradient_from', '#111827'),
            'background_gradient_to' => request()->input('preview_background_gradient_to', '#2563eb'),
            'text_color' => request()->input('preview_text_color', '#111827'),
            'button_color' => request()->input('preview_button_color', '#2563eb'),
            'layout' => 'horizontal_scroll',
            'is_active' => true,
            'sort_order' => -999,
        ]);

        $section->exists = false;
        $section->id = 0;

        if (in_array($type, ['featured_items', 'item_collection'], true)) {
            $section->setRelation(
                'activeSectionItems',
                $this->previewSectionItems($workspace, $section)
            );

            $section->setRelation('activeOffers', collect());

            return $section;
        }

        $section->setRelation('activeSectionItems', collect());
        $section->setRelation('activeOffers', $this->previewOffers($workspace));

        return $section;
    }

    private function previewSectionItems(Workspace $workspace, RestaurantMenuContentSection $section): Collection
    {
        $itemIds = collect((array) request()->input('preview_item_ids', []))
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        if ($itemIds->isEmpty()) {
            $items = $workspace->restaurantMenuItems()
                ->where('is_available', true)
                ->with(['activeVariants', 'activeOptionGroups'])
                ->latest('id')
                ->limit(6)
                ->get();
        } else {
            $items = $workspace->restaurantMenuItems()
                ->whereIn('id', $itemIds)
                ->where('is_available', true)
                ->with(['activeVariants', 'activeOptionGroups'])
                ->get()
                ->sortBy(fn($item) => $itemIds->search($item->id))
                ->values();
        }

        return $items->map(function ($item, $index) use ($workspace, $section) {
            $row = new RestaurantMenuContentSectionItem([
                'workspace_id' => $workspace->id,
                'branch_id' => $section->branch_id,
                'section_id' => 0,
                'item_id' => $item->id,
                'sort_order' => $index,
                'is_active' => true,
            ]);

            $row->setRelation('item', $item);

            return $row;
        });
    }

    private function previewOffers(Workspace $workspace): Collection
    {
        $item = $workspace->restaurantMenuItems()
            ->where('is_available', true)
            ->latest('id')
            ->first();

        $offer = new RestaurantMenuOffer([
            'workspace_id' => $workspace->id,
            'branch_id' => request()->integer('preview_branch_id') ?: null,
            'section_id' => 0,
            'item_id' => $item?->id,
            'title' => request()->input('preview_title', 'عرض اليوم'),
            'subtitle' => request()->input('preview_subtitle', 'خصم خاص لفترة محدودة'),
            'description' => 'هذا عرض تجريبي يظهر فقط داخل المعاينة.',
            'badge_text' => 'Preview',
            'old_price' => $item?->price,
            'new_price' => $item?->sale_price ?: $item?->price,
            'currency' => $item?->currency ?: 'EGP',
            'background_color' => request()->input('preview_background_color', '#111827'),
            'text_color' => request()->input('preview_text_color', '#ffffff'),
            'is_active' => true,
            'sort_order' => 0,
        ]);

        if ($item) {
            $offer->setRelation('item', $item);
        }

        return collect([$offer]);
    }
}
