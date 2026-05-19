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

    public function store(Request $request)
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

    private function workspaceTypeFromSpecification(string $specificationKey): string
    {
        return match ($specificationKey) {
            'restaurant' => 'restaurant',
            'appointments' => 'services',
            'bio' => 'business_page',
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
}