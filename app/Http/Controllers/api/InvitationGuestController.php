<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationGuest;
use Illuminate\Http\Request;

class InvitationGuestController extends Controller
{

// public function store(Request $request, $invitationId)
// {
//     $user = $request->user();

//     $invitation = Invitation::where('user_id', $user->id)
//         ->where('id', $invitationId)
//         ->firstOrFail();

//     $data = $request->validate([
//         'full_name'  => ['required', 'string', 'max:180'],
//         'phone_e164' => ['nullable', 'string', 'max:20'],
//         'email'      => ['nullable', 'email', 'max:191'],
//         'tag'        => ['nullable', 'string', 'max:50'],
//         'notes'      => ['nullable', 'string', 'max:500'],
//     ]);

//     $guest = new InvitationGuest($data);
//     $guest->invitation_id = $invitation->id;
//     // booted في الموديل هيتكفّل بتوليد invite_token + invite_url
//     $guest->save();

//     return response()->json([
//         'status' => 'success',
//         'data'   => $guest,
//     ], 201);
// }


   public function index(Request $request, $invitationId)
    {
        $user = $request->user();

        $invitation = Invitation::where('user_id', $user->id)
            ->where('id', $invitationId)
            ->firstOrFail();

        $guests = InvitationGuest::where('invitation_id', $invitation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Guests loaded successfully',
            'data'    => $guests,
        ]);
    }

    public function store(Request $request, $invitationId)
    {
        $user = $request->user();

        $invitation = Invitation::where('user_id', $user->id)
            ->where('id', $invitationId)
            ->firstOrFail();

        $validated = $request->validate([
            'full_name'  => ['required', 'string', 'max:180'],
            'phone_e164' => ['nullable', 'string', 'max:20'],
            'email'      => ['nullable', 'email', 'max:191'],
            'tag'        => ['nullable', 'string', 'max:50'],
            'notes'      => ['nullable', 'string', 'max:500'],
        ]);

        $guest = new InvitationGuest($validated);
        $guest->invitation_id = $invitation->id;
        // الموديل هيولّد invite_token و invite_url في booted
        $guest->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Guest added successfully',
            'data'    => $guest,
        ], 201);
    }

    public function destroy(Request $request, $guestId)
    {
        $user = $request->user();

        $guest = InvitationGuest::with('invitation')->findOrFail($guestId);

        if ($guest->invitation->user_id !== $user->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'غير مسموح بحذف هذا المدعو',
            ], 403);
        }

        $guest->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Guest deleted successfully',
        ]);
    }


}
