<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\InvitationGuest;
use App\Models\InvitationRsvp;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QrController extends Controller
{
    /**
     * Scan QR code for guest check-in
     *
     * POST /api/qr/scan
     * Body: { "token": "11111111-1111-1111-1111-111111111111" }
     */
    public function scan(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'token' => ['required', 'string'],
        ]);

        // 1) نجيب الضيف عن طريق invite_token
        $guest = InvitationGuest::with(['invitation', 'rsvp'])
            ->where('invite_token', $data['token'])
            ->first();

        if (! $guest) {
            return response()->json([
                'status'  => 'error',
                'message' => 'QR غير صالح أو الضيف غير موجود',
            ], 404);
        }

        $invitation = $guest->invitation;

        // 2) نتأكد إن المستخدم اللي بيسحب الـ QR هو صاحب الدعوة
        if ($invitation->user_id !== $user->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'غير مسموح لك بإدارة هذه الدعوة',
            ], 403);
        }

        /** @var InvitationRsvp|null $rsvp */
        $rsvp = $guest->rsvp;

        // 3) تحديد حالة الـ scan
        $scanStatus = 'ok'; // ok | no_rsvp | declined | already_checked_in

        if (! $rsvp) {
            $scanStatus = 'no_rsvp'; // ما ردش على الدعوة
        } elseif ($rsvp->response === 'declined') {
            $scanStatus = 'declined'; // معتذر
        }

        $alreadyCheckedIn = false;
        if ($rsvp && $rsvp->checked_in_at) {
            $alreadyCheckedIn = true;
            $scanStatus = 'already_checked_in';
        }

        // 4) تعليم الحضور (check-in) لو Accepted ولسه ما اتعلمش
        if ($rsvp && $rsvp->response === 'accepted' && ! $alreadyCheckedIn) {
            $rsvp->checked_in_at = Carbon::now();
            $rsvp->checked_in_by = $user->id;
            $rsvp->save();
        }

        // نعيد تحميل الـ rsvp بعد التحديث
        if ($rsvp) {
            $rsvp->refresh();
        }

        // 5) بناء البيانات للواجهة
        $guestData = [
            'id'          => $guest->id,
            'full_name'   => $guest->full_name,
            'phone'       => $guest->phone_e164,
            'email'       => $guest->email,
            'tag'         => $guest->tag,
        ];

        $invitationData = [
            'id'          => $invitation->id,
            'title'       => $invitation->title,
            'groom_name'  => $invitation->groom_name,
            'bride_name'  => $invitation->bride_name,
            'event_date'  => $invitation->event_date ? $invitation->event_date->toDateString() : null,
            'event_time'  => $invitation->event_time,
            'venue_name'  => $invitation->venue_name,
            'slug'        => $invitation->slug,
        ];

        $rsvpData = null;
        if ($rsvp) {
            $rsvpData = [
                'id'               => $rsvp->id,
                'response'         => $rsvp->response,             // accepted | declined
                'companions_count' => (int) $rsvp->companions_count,
                'companions'       => $rsvp->companions_json,
                'message'          => $rsvp->message,
                'responded_at'     => optional($rsvp->responded_at)->toDateTimeString(),
                'checked_in_at'    => optional($rsvp->checked_in_at)->toDateTimeString(),
                'checked_in_by'    => $rsvp->checked_in_by,
            ];
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'QR تم مسحه بنجاح',
            'scan_status' => $scanStatus,
            'guest'   => $guestData,
            'invitation' => $invitationData,
            'rsvp'    => $rsvpData,
        ]);
    }
}
