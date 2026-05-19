<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FcmService
{
    protected string $projectId;
    protected string $serviceAccountPath;

    public function __construct()
    {
        $this->projectId        = config('services.firebase.project_id', 'quickmart-fbc37');
        $this->serviceAccountPath = config('services.firebase.service_account')
            ?: storage_path('app/firebase/quickmart-fbc37-firebase-adminsdk-fbsvc-f9f75f4c01.json');
    }

    /**
     * إرسال إشعار إلى جهاز معيّن عن طريق fcm_token
     */
    public function sendToToken(string $token, array $data = [], ?array $notification = null): bool
    {
        $accessToken = $this->getAccessToken();
        if (! $accessToken) {
            return false;
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'data'  => $data,
            ],
        ];

        if ($notification) {
            $payload['message']['notification'] = $notification;
        }

        $response = Http::withToken($accessToken)->post($url, $payload);

        return $response->successful();
    }

    /**
     * إرسال إشعار إلى Topic معيّن
     */
    public function sendToTopic(string $topic, array $data = [], ?array $notification = null): bool
    {
        $accessToken = $this->getAccessToken();
        if (! $accessToken) {
            return false;
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [
            'message' => [
                'topic' => $topic,
                'data'  => $data,
            ],
        ];

        if ($notification) {
            $payload['message']['notification'] = $notification;
        }

        $response = Http::withToken($accessToken)->post($url, $payload);

        return $response->successful();
    }

    /**
     * نفس فكرة getAccessToken اللي عندك لكن مع caching لمدة ~55 دقيقة
     */
    protected function getAccessToken(): ?string
    {
        return Cache::remember('fcm_access_token', 55 * 60, function () {
            if (! file_exists($this->serviceAccountPath)) {
                return null;
            }

            $googleAuthUrl   = "https://oauth2.googleapis.com/token";
            $serviceAccount  = json_decode(file_get_contents($this->serviceAccountPath), true);

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
        });
    }

    



    /**
     * إرسال إشعار ويب إلى جهاز معيّن عن طريق fcm_token
     */
    public function sendToTokenWeb(string $token, array $data = [], ?array $notification = null, ?string $link = null): array
{
    $accessToken = $this->getAccessToken();
    if (! $accessToken) {
        return ['success' => false, 'status' => 0, 'body' => 'No access token'];
    }

    // FCM data لازم تكون string values
    $data = collect($data)->map(fn($v) => (string) $v)->all();

    $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

    $message = [
        'token' => $token,
        'data'  => $data,
    ];

    if ($notification) {
        $message['notification'] = $notification;

        // WebPush notification (يزود فرص ظهورها في PWA)
        $message['webpush'] = [
            'notification' => [
                'title' => $notification['title'] ?? '',
                'body'  => $notification['body'] ?? '',
            ],
        ];
    }

    if ($link) {
        $message['webpush']['fcm_options'] = ['link' => $link];
    }

    $payload = ['message' => $message];

    $response = Http::withToken($accessToken)->post($url, $payload);

    return [
        'success' => $response->successful(),
        'status'  => $response->status(),
        'body'    => $response->body(),
        'name'    => $response->json('name'),
    ];
}

}
