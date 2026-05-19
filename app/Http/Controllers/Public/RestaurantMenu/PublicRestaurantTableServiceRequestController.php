<?php

namespace App\Http\Controllers\Public\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Models\RestaurantMenu\RestaurantBranch;
use App\Models\RestaurantMenu\RestaurantTable;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PublicRestaurantTableServiceRequestController extends Controller
{
    public function store(Request $request, Workspace $workspace, RestaurantBranch $branch)
    {
        abort_if((int) $branch->workspace_id !== (int) $workspace->id, 404);

        $data = $request->validate([
            'type' => ['required', Rule::in(['waiter', 'cash'])],
            'table_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $table = null;

        if (! empty($data['table_id'])) {
            $table = RestaurantTable::query()
                ->where('workspace_id', $workspace->id)
                ->where('branch_id', $branch->id)
                ->where('id', $data['table_id'])
                ->first();
        }

        if (! $table && $request->filled('table_code')) {
            $table = RestaurantTable::query()
                ->where('workspace_id', $workspace->id)
                ->where('branch_id', $branch->id)
                ->where('code', $request->input('table_code'))
                ->first();
        }

        if (! $table) {
            return back()->with('error', 'لم يتم تحديد الطاولة.');
        }

        $guestToken = $request->session()->get('restaurant_guest_token');

        if (! $guestToken) {
            $guestToken = (string) Str::uuid();
            $request->session()->put('restaurant_guest_token', $guestToken);
        }

        /*
         * منع تكرار الضغط بسرعة:
         * لو نفس الطاولة طلبت نفس الخدمة خلال آخر دقيقتين،
         * لا ننشئ طلبًا جديدًا.
         */
        $recentRequest = $workspace->restaurantTableServiceRequests()
            ->where('branch_id', $branch->id)
            ->where('table_id', $table->id)
            ->where('type', $data['type'])
            ->whereIn('status', ['new', 'seen'])
            ->where('created_at', '>=', now()->subMinutes(2))
            ->latest('id')
            ->first();

        if ($recentRequest) {
            return back()->with('success', 'تم إرسال الطلب بالفعل، سيصل إليك أحد أفراد الفريق قريبًا.');
        }

        $workspace->restaurantTableServiceRequests()->create([
            'branch_id' => $branch->id,
            'table_id' => $table->id,
            'type' => $data['type'],
            'status' => 'new',
            'table_number' => $table->number,
            'table_name' => $table->name,
            'guest_token' => $guestToken,
            'notes' => $data['notes'] ?? null,
        ]);

        $message = $data['type'] === 'waiter'
            ? 'تم طلب الجرسون بنجاح.'
            : 'تم طلب الحساب بنجاح.';

        return back()->with('success', $message);
    }
}