<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminNotifyController extends Controller
{
    public function sendNotificationToTopicPost(Request $request)
    {
        $data = $request->validate([
            'head'   => 'required|string|max:255',
            'desc'   => 'required|string|max:2000',
            'topic'  => 'nullable|string|max:100',   // تقدر تختار من الفورم
            'mtopic' => 'nullable|string|max:100',   // لو عايز نوع مخصص
            'action' => 'nullable|string|max:50',    // open / request_location / orders ..
            'url'    => 'nullable|string|max:500',
        ]);

        // خليك متفق مع الموبايل على اسم التوبك
        // مثلاً: drivers_app  أو sonic_driver
        $topic = $data['topic'] ?: 'driver'; // أو 'wheely' لو هتغيّر في التطبيق

        $projectId = 'quickmart-fbc37'; // أو من config('services.firebase.project_id')

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return back()->with('error', 'فشل في الحصول على access token من FCM');
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $payload = [
            'message' => [
                'topic' => $topic,
                // هنا بنستخدم data-only message
                'data'  => [
                    'head'   => $data['head'],
                    'desc'   => $data['desc'],
                    'mtopic' => $data['mtopic'] ?? '',
                    'action' => $data['action'] ?? 'open', // مثلاً: request_location
                    'url'    => $data['url'] ?? '',
                ],
                // لو عايز كمان notification عشان تظهر سيستمياً بدون ما تعمل local notif:
                // 'notification' => [
                //     'title' => $data['head'],
                //     'body'  => $data['desc'],
                // ],
            ],
        ];

        $response = Http::withToken($accessToken)
            ->post($url, $payload);

        if (! $response->successful()) {
            return back()->with(
                'error',
                'فشل إرسال الإشعار: ' . $response->body()
            );
        }

        return back()->with('success', 'تم إرسال الإشعار بنجاح.');
    }

    public function getAccessToken(): ?string
    {
        $keyFilePath = storage_path('app/firebase/quickmart-fbc37-firebase-adminsdk-fbsvc-f9f75f4c01.json');

        if (! file_exists($keyFilePath)) {
            return null;
        }

        $googleAuthUrl = "https://oauth2.googleapis.com/token";

        $serviceAccount = json_decode(file_get_contents($keyFilePath), true);

        $header = base64_encode(json_encode([
            "alg" => "RS256",
            "typ" => "JWT",
        ]));

        $iat = time();
        $exp = $iat + 3600;

        $payload = base64_encode(json_encode([
            "iss"   => $serviceAccount["client_email"],
            "scope" => "https://www.googleapis.com/auth/firebase.messaging",
            "aud"   => $googleAuthUrl,
            "exp"   => $exp,
            "iat"   => $iat,
        ]));

        $signature = '';
        openssl_sign(
            "{$header}.{$payload}",
            $signature,
            openssl_pkey_get_private($serviceAccount["private_key"]),
            OPENSSL_ALGO_SHA256
        );

        $jwtAssertion = "{$header}.{$payload}." . base64_encode($signature);

        $response = Http::post($googleAuthUrl, [
            "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
            "assertion"  => $jwtAssertion,
        ]);

        if (! $response->successful()) {
            return null;
        }

        return $response->json()['access_token'] ?? null;
    }

    public function pushForm()
{
    return view('admin.sections.settings.push_notify');
}


public function sendNotificationToDevicePost(Request $request)
    {
        $data = $request->validate([
            'fcm_token' => 'required|string',
            'head'      => 'required|string|max:255',
            'desc'      => 'required|string|max:2000',
            'mtopic'    => 'nullable|string|max:100',
            'action'    => 'nullable|string|max:50',
            'url'       => 'nullable|string|max:500',
        ]);

        $projectId   = 'quickmart-fbc37'; // أو من config
        $accessToken = $this->getAccessToken();

        if (! $accessToken) {
            return back()->with('error', 'فشل في الحصول على Access Token من FCM.');
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $data['fcm_token'],
                'data'  => [
                    'head'   => $data['head'],
                    'desc'   => $data['desc'],
                    'mtopic' => $data['mtopic'] ?? '',
                    'action' => $data['action'] ?? 'open',
                    'url'    => $data['url'] ?? '',
                ],
                // لو عايز notification system كمان:
                // 'notification' => [
                //     'title' => $data['head'],
                //     'body'  => $data['desc'],
                // ],
            ],
        ];

        $response = Http::withToken($accessToken)
            ->post($url, $payload);

        if (! $response->successful()) {
            return back()->with('error', 'فشل إرسال الإشعار: ' . $response->body());
        }

        return back()->with('success', 'تم إرسال الإشعار للجهاز بنجاح.');
    }

}
