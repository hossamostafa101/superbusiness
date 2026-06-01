<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Specification;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{

public function create()
{
    $user = auth('web')->user();

    $workspace = $user->ownedWorkspaces()
        ->with('specification')
        ->latest('id')
        ->first();

    if ($workspace) {
        if (! $workspace->onboarding_completed_at) {
            return $this->redirectToOnboardingStep($workspace);
        }

        return redirect()->to($this->workspaceRedirectUrl($workspace));
    }

    $specifications = Specification::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get(['id', 'name', 'key', 'description']);

    return view('onboarding.steps.business', compact('specifications'));
}
    public function createX()
    {
        $user = auth('web')->user();

        $workspace = $user->ownedWorkspaces()
            ->latest('id')
            ->first();

        if ($workspace) {
            return redirect()->to($this->workspaceRedirectUrl($workspace));
        }

        $specifications = Specification::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'key', 'description']);

        return view('onboarding.create', compact('specifications'));
    }

    public function storeBusiness(Request $request)
{
    $user = auth('web')->user();

    $data = $request->validate([
        'business_name' => ['required', 'string', 'max:150'],
        'specification_id' => ['required', 'integer', 'exists:specifications,id'],
    ]);

    $specification = Specification::query()
        ->where('is_active', true)
        ->findOrFail($data['specification_id']);

    $workspace = DB::transaction(function () use ($user, $data, $specification) {
        $slug = $this->uniqueWorkspaceSlug($data['business_name']);

        $workspace = Workspace::create([
            'owner_id' => $user->id,
            'specification_id' => $specification->id,
            'name' => $data['business_name'],
            'slug' => $slug,
            'type' => $this->workspaceTypeFromSpecification($specification->key),
            'status' => 'active',
            'trial_ends_at' => now()->addDays(14),
            'onboarding_step' => 'profile',
        ]);

        app(\Modules\Affiliate\Services\AffiliateTrackingService::class)
    ->attachWorkspace($user, $workspace);

        $workspace->users()->syncWithoutDetaching([
            $user->id => [
                'role' => 'owner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $workspace->businessProfile()->create([
            'display_name' => $data['business_name'],
            'theme_color' => '#111827',
            'button_color' => '#2563eb',
            'text_color' => '#111827',
            'is_published' => true,
        ]);

        $freePlan = Plan::where('slug', 'free')
            ->where('is_active', true)
            ->first();

        if ($freePlan) {
            $workspace->subscriptions()->create([
                'plan_id' => $freePlan->id,
                'status' => 'trialing',
                'billing_cycle' => 'monthly',
                'starts_at' => now(),
                'trial_ends_at' => now()->addDays(14),
                'ends_at' => null,
            ]);
        }

        return $workspace;
    });

    return redirect()->route('onboarding.profile');
}
    public function storeX(Request $request)
    {
        $user = auth('web')->user();

        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:150'],

            'specification_id' => [
                'required',
                'integer',
                'exists:specifications,id',
            ],

            'whatsapp_number' => ['nullable', 'string', 'max:30'],
        ]);

        $specification = Specification::query()
            ->where('is_active', true)
            ->findOrFail($data['specification_id']);

        $workspace = DB::transaction(function () use ($user, $data, $specification) {
            $slug = Str::slug($data['business_name']);

            if (! $slug) {
                $slug = 'business-' . Str::random(6);
            }

            $originalSlug = $slug;
            $counter = 1;

            while (Workspace::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $workspace = Workspace::create([
                'owner_id' => $user->id,
                'specification_id' => $specification->id,
                'name' => $data['business_name'],
                'slug' => $slug,

                // خلي type متوافق مع القديم لو مستخدم في أماكن أخرى
                'type' => $this->workspaceTypeFromSpecification($specification->key),

                'status' => 'active',
                'trial_ends_at' => now()->addDays(14),
            ]);

            $workspace->users()->syncWithoutDetaching([
                $user->id => [
                    'role' => 'owner',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            $workspace->businessProfile()->create([
                'display_name' => $data['business_name'],
                'whatsapp_number' => $data['whatsapp_number'] ?? null,
                'theme_color' => '#111827',
                'button_color' => '#2563eb',
                'text_color' => '#111827',
                'is_published' => true,
            ]);

            $freePlan = Plan::where('slug', 'free')
                ->where('is_active', true)
                ->first();

            if ($freePlan) {
                $workspace->subscriptions()->create([
                    'plan_id' => $freePlan->id,
                    'status' => 'trialing',
                    'billing_cycle' => 'monthly',
                    'starts_at' => now(),
                    'trial_ends_at' => now()->addDays(14),
                    'ends_at' => null,
                ]);
            }

            return $workspace->load('specification');
        });

        return redirect()
            ->to($this->workspaceRedirectUrl($workspace))
            ->with('success', 'تم إنشاء مساحة العمل بنجاح.');
    }

    public function profile()
{
    $workspace = $this->currentOnboardingWorkspace();

    $workspace->load(['businessProfile', 'businessLinks', 'specification']);

    return view('onboarding.steps.profile', compact('workspace'));
}

public function storeProfile(Request $request)
{
    $workspace = $this->currentOnboardingWorkspace();

    $data = $request->validate([
        'display_name' => ['required', 'string', 'max:150'],
        'tagline' => ['nullable', 'string', 'max:255'],
        'description' => ['nullable', 'string', 'max:2000'],

        'whatsapp_number' => ['nullable', 'string', 'max:40'],
        'phone' => ['nullable', 'string', 'max:40'],
        'email' => ['nullable', 'email', 'max:255'],
        'address' => ['nullable', 'string', 'max:255'],
        'location_url' => ['nullable', 'url', 'max:2000'],

        'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

        'links' => ['nullable', 'array'],
        'links.instagram' => ['nullable', 'url', 'max:2000'],
        'links.facebook' => ['nullable', 'url', 'max:2000'],
        'links.tiktok' => ['nullable', 'url', 'max:2000'],
        'links.website' => ['nullable', 'url', 'max:2000'],
    ]);

    DB::transaction(function () use ($workspace, $request, $data) {
        $profileData = [
            'display_name' => $data['display_name'],
            'tagline' => $data['tagline'] ?? null,
            'description' => $data['description'] ?? null,
            'whatsapp_number' => $data['whatsapp_number'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'location_url' => $data['location_url'] ?? null,
            'is_published' => true,
        ];

        if ($request->hasFile('logo')) {
            $profileData['logo'] = $request->file('logo')
                ->store('business-profiles/logos', 'public');
        }

        if ($request->hasFile('cover_image')) {
            $profileData['cover_image'] = $request->file('cover_image')
                ->store('business-profiles/covers', 'public');
        }

        $workspace->businessProfile()->updateOrCreate(
            ['workspace_id' => $workspace->id],
            $profileData
        );

        $this->syncBusinessLinks($workspace, $data['links'] ?? []);

        if ($workspace->specificationKey() === 'restaurant') {
            $workspace->update(['onboarding_step' => 'restaurant_branch']);
        } else {
            $workspace->update([
                'onboarding_step' => 'completed',
                'onboarding_completed_at' => now(),
            ]);
        }
    });

    if ($workspace->fresh()->specificationKey() === 'restaurant') {
        return redirect()->route('onboarding.restaurant-branch');
    }

    return redirect()
        ->to($this->workspaceRedirectUrl($workspace->fresh()))
        ->with('success', 'تم تجهيز بيانات النشاط بنجاح.');
}

private function syncBusinessLinks(Workspace $workspace, array $links): void
{
    $map = [
        'instagram' => ['title' => 'Instagram', 'icon' => 'bi-instagram', 'sort_order' => 10],
        'facebook' => ['title' => 'Facebook', 'icon' => 'bi-facebook', 'sort_order' => 20],
        'tiktok' => ['title' => 'TikTok', 'icon' => 'bi-tiktok', 'sort_order' => 30],
        'website' => ['title' => 'Website', 'icon' => 'bi-globe', 'sort_order' => 40],
    ];

    foreach ($map as $key => $meta) {
        $url = $links[$key] ?? null;

        $existing = $workspace->businessLinks()
            ->where('icon', $meta['icon'])
            ->first();

        if (! $url) {
            if ($existing) {
                $existing->update(['is_active' => false]);
            }

            continue;
        }

        $workspace->businessLinks()->updateOrCreate(
            ['icon' => $meta['icon']],
            [
                'title' => $meta['title'],
                'url' => $url,
                'sort_order' => $meta['sort_order'],
                'is_active' => true,
            ]
        );
    }
}

public function restaurantBranch()
{
    $workspace = $this->currentOnboardingWorkspace();

    abort_unless($workspace->specificationKey() === 'restaurant', 404);

    $workspace->load(['businessProfile', 'restaurantBranches']);

    return view('onboarding.steps.restaurant_branch', compact('workspace'));
}

public function storeRestaurantBranch(Request $request)
{
    $workspace = $this->currentOnboardingWorkspace();

    abort_unless($workspace->specificationKey() === 'restaurant', 404);

    $data = $request->validate([
        'branch_name' => ['required', 'string', 'max:150'],
        'branch_phone' => ['nullable', 'string', 'max:40'],
        'branch_whatsapp_number' => ['nullable', 'string', 'max:40'],
        'address' => ['nullable', 'string', 'max:255'],
        'location_url' => ['nullable', 'url', 'max:2000'],

        'orders_enabled' => ['nullable', 'boolean'],
        'dine_in_enabled' => ['nullable', 'boolean'],
        'takeaway_enabled' => ['nullable', 'boolean'],
        'delivery_enabled' => ['nullable', 'boolean'],
        'whatsapp_orders_enabled' => ['nullable', 'boolean'],
        'open_invoice_enabled' => ['nullable', 'boolean'],
    ]);

    DB::transaction(function () use ($workspace, $data) {
        $branchSlug = Str::slug($data['branch_name']) ?: 'main-branch';

        $originalSlug = $branchSlug;
        $counter = 1;

        while ($workspace->restaurantBranches()->where('slug', $branchSlug)->exists()) {
            $branchSlug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $workspace->restaurantBranches()->create([
            'name' => $data['branch_name'],
            'slug' => $branchSlug,
            'phone' => $data['branch_phone'] ?? null,
            'whatsapp_number' => $data['branch_whatsapp_number'] ?? null,
            'address' => $data['address'] ?? null,
            'location_url' => $data['location_url'] ?? null,
            'is_active' => true,
            'is_default' => true,
            'sort_order' => 0,
        ]);

        $settings = [
            'orders_enabled' => ! empty($data['orders_enabled']) ? '1' : '0',
            'dine_in_enabled' => ! empty($data['dine_in_enabled']) ? '1' : '0',
            'takeaway_enabled' => ! empty($data['takeaway_enabled']) ? '1' : '0',
            'delivery_enabled' => ! empty($data['delivery_enabled']) ? '1' : '0',
            'whatsapp_orders_enabled' => ! empty($data['whatsapp_orders_enabled']) ? '1' : '0',
            'open_invoice_enabled' => ! empty($data['open_invoice_enabled']) ? '1' : '0',
        ];

        foreach ($settings as $key => $value) {
            $workspace->restaurantMenuSettings()->updateOrCreate(
                [
                    'workspace_id' => $workspace->id,
                    'branch_id' => null,
                    'key' => $key,
                ],
                [
                    'value' => $value,
                ]
            );
        }

        $workspace->update([
            'onboarding_step' => 'completed',
            'onboarding_completed_at' => now(),
        ]);
    });

    return redirect()
        ->to($this->workspaceRedirectUrl($workspace->fresh()))
        ->with('success', 'تم تجهيز منيو المطعم بنجاح.');
}


private function currentOnboardingWorkspace(): Workspace
{
    $user = auth('web')->user();

    $workspace = $user->ownedWorkspaces()
        ->with('specification')
        ->latest('id')
        ->firstOrFail();

    return $workspace;
}

    private function uniqueWorkspaceSlug(string $name): string
{
    $slug = Str::slug($name);

    if (! $slug) {
        $slug = 'business-' . Str::random(6);
    }

    $originalSlug = $slug;
    $counter = 1;

    while (Workspace::where('slug', $slug)->exists()) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }

    return $slug;
}

    private function workspaceTypeFromSpecification(string $specificationKey): string
    {
        return match ($specificationKey) {
            'restaurant' => 'restaurant',
            'appointments' => 'services',
            'bio' => 'business_page',
            'medical' => 'medical',
            default => 'business_page',
        };
    }

    private function workspaceRedirectUrl(Workspace $workspace): string
    {
        $workspace->loadMissing('specification');

        return match ($workspace->specificationKey()) {
            // 'restaurant' => route('app.restaurant-menu.branches.index', $workspace),
            'restaurant' => route('app.restaurant-menu.dashboard', $workspace),
            'appointments' => route('app.business-profile.edit', $workspace),
            'bio' => route('app.business-profile.edit', $workspace),
            default => route('app.business-profile.edit', $workspace),
        };
    }

    private function redirectToOnboardingStep(Workspace $workspace)
{
    return match ($workspace->onboarding_step) {
        'profile' => redirect()->route('onboarding.profile'),
        'restaurant_branch' => redirect()->route('onboarding.restaurant-branch'),
        'finish' => redirect()->route('onboarding.profile'),
        default => redirect()->route('onboarding.profile'),
    };
}
}