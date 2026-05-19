<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Models\RestaurantMenu\RestaurantTableServiceRequest;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RestaurantTableServiceRequestController extends Controller
{
    public function index(Request $request, Workspace $workspace)
    {
        $serviceRequests = $workspace->restaurantTableServiceRequests()
            ->with(['branch:id,name', 'table:id,name,number'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('app.restaurant-menu.service-requests.index', compact(
            'workspace',
            'serviceRequests'
        ));
    }

    public function updateStatus(Request $request, Workspace $workspace, RestaurantTableServiceRequest $serviceRequest)
    {
        abort_if((int) $serviceRequest->workspace_id !== (int) $workspace->id, 404);

        $data = $request->validate([
            'status' => ['required', Rule::in(['new', 'seen', 'done', 'cancelled'])],
        ]);

        $payload = [
            'status' => $data['status'],
        ];

        if ($data['status'] === 'seen' && ! $serviceRequest->seen_at) {
            $payload['seen_at'] = now();
        }

        if ($data['status'] === 'done') {
            $payload['done_at'] = now();

            if (! $serviceRequest->seen_at) {
                $payload['seen_at'] = now();
            }
        }

        $serviceRequest->update($payload);

        return back()->with('success', 'تم تحديث حالة الطلب.');
    }
}