<?php
// app/Http/Controllers/PublicAgentReplyController.php
namespace App\Http\Controllers;

use App\Models\{AvailabilityRequest, AvailabilityRequestItem};
use Illuminate\Http\Request;

class PublicAgentReplyController extends Controller
{
    public function form($token)
    {
        $req = AvailabilityRequest::with(['agent','items.leg.hotel','items.hotel'])
                ->where('public_token',$token)->firstOrFail();

        abort_if(!in_array($req->status, ['sent','answered']), 403, 'الطلب غير متاح للرد حالياً.');

        return view('public.agent_reply', compact('req'));
    }

    public function submit(Request $request, $token)
    {
        $req = AvailabilityRequest::with('items')->where('public_token',$token)->firstOrFail();
        abort_if(!in_array($req->status, ['sent','answered']), 403);

        $data = $request->validate([
            'items'                                   => ['required','array'],
            'items.*.id'                              => ['required','exists:availability_request_items,id'],
            'items.*.reply_status'                    => ['required','in:available,partial,not_available'],
            'items.*.reply_available_rooms'           => ['nullable','integer','min:0'],
            'items.*.reply_net_rate'                  => ['nullable','numeric','min:0'],
            'items.*.reply_currency'                  => ['nullable','string','max:8'],
            'items.*.reply_notes'                     => ['nullable','string','max:2000'],
        ]);

        foreach ($data['items'] as $itemData) {
            $item = $req->items->firstWhere('id', $itemData['id']);
            if (!$item) continue;

            $item->update([
                'reply_status'          => $itemData['reply_status'],
                'reply_available_rooms' => $itemData['reply_available_rooms'] ?? null,
                'reply_net_rate'        => $itemData['reply_net_rate'] ?? null,
                'reply_currency'        => $itemData['reply_currency'] ?? null,
                'reply_notes'           => $itemData['reply_notes'] ?? null,
                'reply_at'              => now(),
            ]);
        }

        // لو دي أول مرة يرد فيها أي بند
        if ($req->status !== 'answered') {
            $req->update(['status' => 'answered', 'responded_at' => now()]);
        }

        return back()->with('success','تم حفظ الرد. شكرًا لتعاونكم.');
    }
}
