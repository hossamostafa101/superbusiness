<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\FcmService;

class EmojiNotifyController extends Controller
{
    public function notifyParent(Request $request, FcmService $fcm)
    {
        $validated = $request->validate([
            'parent_token' => 'required|string',
            'emoji'        => 'required|string|max:16',
            'label'        => 'nullable|string|max:64',
            'child_name'   => 'nullable|string|max:64',
        ]);

        $token = $validated['parent_token'];
        $emoji = $validated['emoji'];
        $label = $validated['label'] ?? '';
        $child = $validated['child_name'] ?? 'Child';

        $title = 'تنبيه من الطفل';
        $body  = trim($child . ' ' . ($label !== '' ? $label : $emoji) . ' ' . $emoji);

        // مهم: FCM data لازم تكون string=>string
        $data = [
            'emoji' => (string) $emoji,
            'label' => (string) $label,
            'child' => (string) $child,
            'type'  => 'emoji',
        ];

        // ده اللي بيخلي الإشعار يظهر فعليًا
        $notification = [
            'title' => $title,
            'body'  => $body,
        ];

        // إرسال
        $ok = $fcm->sendToTokenWeb($token, $data, $notification, 'https://twseela.com/arabas/public/fahmeenak5/');
            
            //  $fcm->sendToToken($token, [
            //         'action'   => 'unassigned_order_reminder',
            //         'order_id' => 'sdfsdff',
            //         'head'     => 'asdasdasd',
            //         'desc'     => 'sffsdfsfsdf',
            //     ]);

        if (! $ok['success']) {
            Log::error('FCM send failed', [
                'status' => $ok['status'],
                'body'   => $ok['body'],
            ]);

            return response()->json([
                'success' => false,
                'status'  => $ok['status'],
                'error'   => $ok['body'],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'name'    => $ok['name'] ?? null,
        ]);
    }
}
