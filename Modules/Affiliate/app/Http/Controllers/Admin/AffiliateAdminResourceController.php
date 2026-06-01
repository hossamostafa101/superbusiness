<?php

namespace Modules\Affiliate\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Modules\Affiliate\Models\AffiliateResource;

class AffiliateAdminResourceController extends Controller
{
    public function index(Request $request)
    {
        $resources = AffiliateResource::query()
            ->with('specification')
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->when($request->filled('specification_id'), function ($query) use ($request) {
                $query->where('specification_id', $request->input('specification_id'));
            })
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        $specifications = Specification::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('affiliate::admin.resources.index', compact(
            'resources',
            'specifications'
        ));
    }

    public function create()
    {
        $specifications = Specification::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('affiliate::admin.resources.create', compact('specifications'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('affiliate/resources', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');

        AffiliateResource::query()->create($data);

        return redirect()
            ->route('admin.affiliate.resources.index')
            ->with('success', 'تم إضافة المورد التسويقي.');
    }

    public function edit(AffiliateResource $resource)
    {
        $specifications = Specification::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('affiliate::admin.resources.edit', compact(
            'resource',
            'specifications'
        ));
    }

    public function update(Request $request, AffiliateResource $resource)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('file')) {
            if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
                Storage::disk('public')->delete($resource->file_path);
            }

            $data['file_path'] = $request->file('file')->store('affiliate/resources', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');

        $resource->update($data);

        return redirect()
            ->route('admin.affiliate.resources.index')
            ->with('success', 'تم تحديث المورد التسويقي.');
    }

    public function destroy(AffiliateResource $resource)
    {
        if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return back()->with('success', 'تم حذف المورد.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'specification_id' => [
                'nullable',
                Rule::exists('specifications', 'id'),
            ],
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'type' => [
                'required',
                Rule::in([
                    'text',
                    'link',
                    'video',
                    'image',
                    'pdf',
                    'demo',
                    'whatsapp_script',
                    'other',
                ]),
            ],
            'content' => ['nullable', 'string'],
            'url' => ['nullable', 'url', 'max:500'],
            'file' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}