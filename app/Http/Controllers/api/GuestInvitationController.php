<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\InvitationEvent;
use App\Models\InvitationGuest;
use App\Models\InvitationRsvp;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GuestInvitationController extends Controller
{
    /**
     * عرض صفحة الدعوة للضيف + تسجيل open
     *
     * GET /api/guest/invitations/{token}
     */
    public function show(Request $request, $token)
    {
        $guest = InvitationGuest::with(['invitation', 'rsvp'])
            ->where('invite_token', $token)
            ->firstOrFail();

        $invitation = $guest->invitation;

        // لو الدعوة مش منشورة / ملغية
        if (! $invitation->is_published) {
            return response()->json([
                'status'  => 'error',
                'message' => 'هذه الدعوة غير متاحة حالياً',
            ], 410); // Gone
        }

        // تسجيل حدث فتح (open)
        InvitationEvent::create([
            'invitation_id' => $invitation->id,
            'guest_id'      => $guest->id,
            'event_name'    => 'open',
            'ip_address'    => $request->ip() ? inet_pton($request->ip()) : null,
            'user_agent'    => substr($request->userAgent() ?? '', 0, 500),
            'occurred_at'   => Carbon::now(),
        ]);

        $date = $invitation->event_date
            ? $invitation->event_date->toDateString()
            : null;

        $rawTime = $invitation->event_time;
        $time    = $rawTime ? substr((string) $rawTime, 0, 5) : null;

        $rsvp = $guest->rsvp;

        $rsvpData = null;
        if ($rsvp) {
            $rsvpData = [
                'response'         => $rsvp->response, // accepted | declined
                'companions_count' => (int) $rsvp->companions_count,
                'companions'       => $rsvp->companions_json,
                'message'          => $rsvp->message,
                'responded_at'     => optional($rsvp->responded_at)->toDateTimeString(),
            ];
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Invitation loaded successfully',
            'data'    => [
                'guest' => [
                    'id'        => $guest->id,
                    'full_name' => $guest->full_name,
                    'tag'       => $guest->tag,
                ],
                'invitation' => [
                    'id'          => $invitation->id,
                    'title'       => $invitation->title,
                    'groom_name'  => $invitation->groom_name,
                    'bride_name'  => $invitation->bride_name,
                    'event_date'  => $date,
                    'event_time'  => $time,
                    'venue_name'  => $invitation->venue_name,
                    'location_url'=> $invitation->location_url,
                    'hero_media_url' => $invitation->hero_media_url,
                    'message_text'=> $invitation->message_text,
                    'slug'        => $invitation->slug,
                ],
                'rsvp' => $rsvpData,
            ],
        ]);
    }

    /**
     * إرسال رد الضيف (قبول / اعتذار + المرافقين)
     *
     * POST /api/guest/invitations/{token}/rsvp
     */
    public function submitRsvp(Request $request, $token)
    {
        $guest = InvitationGuest::with('invitation')
            ->where('invite_token', $token)
            ->firstOrFail();

        $invitation = $guest->invitation;

        if (! $invitation->is_published) {
            return response()->json([
                'status'  => 'error',
                'message' => 'هذه الدعوة غير متاحة حالياً',
            ], 410);
        }

        $data = $request->validate([
            'response'         => ['required', 'in:accepted,declined'],
            'companions_count' => ['nullable', 'integer', 'min:0', 'max:20'],
            'companions'       => ['nullable', 'array'],
            'companions.*'     => ['nullable', 'string', 'max:191'],
            'message'          => ['nullable', 'string', 'max:500'],
        ]);

        $companionsCount = $data['companions_count'] ?? 0;
        $companions      = $data['companions'] ?? null;

        // لو معتذر، المنطقي companions_count = 0
        if ($data['response'] === 'declined') {
            $companionsCount = 0;
            $companions      = null;
        }

        // نجيب أو ننشئ RSVP
        $rsvp = InvitationRsvp::firstOrNew([
            'invitation_id' => $invitation->id,
            'guest_id'      => $guest->id,
        ]);

        $rsvp->response         = $data['response'];
        $rsvp->companions_count = $companionsCount;
        $rsvp->companions_json  = $companions;
        $rsvp->message          = $data['message'] ?? null;
        $rsvp->responded_at     = Carbon::now();
        $rsvp->save();

        // تسجيل event rsvp_submit
        InvitationEvent::create([
            'invitation_id' => $invitation->id,
            'guest_id'      => $guest->id,
            'event_name'    => 'rsvp_submit',
            'ip_address'    => $request->ip() ? inet_pton($request->ip()) : null,
            'user_agent'    => substr($request->userAgent() ?? '', 0, 500),
            'occurred_at'   => Carbon::now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'RSVP submitted successfully',
            'data'    => [
                'response'         => $rsvp->response,
                'companions_count' => (int) $rsvp->companions_count,
                'companions'       => $rsvp->companions_json,
                'message'          => $rsvp->message,
                'responded_at'     => $rsvp->responded_at->toDateTimeString(),
            ],
        ]);
    }
}
