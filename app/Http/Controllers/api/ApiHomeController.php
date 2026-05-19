<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\File;
use App\Models\Image;
use App\Models\PatientCase;
use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApiHomeController extends Controller
{
   function sendNotificationToTopic(Request $request)
{
    $projectId = 'quickmart-af5b6'; // Change to your Firebase project ID
    $accessToken = $request->access_token; // Change to your service account private key
    if (!$accessToken) {
        return ["error" => "Failed to retrieve access token"];
    }
        
    $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

    $data = [
        "message" => [
            "topic" => $request->topic,
            // "notification" => [
            //     "title" => 'dcdcdcdcd',
            //     "body" => 'bgbgbgbgbgbg',
            // ],
            "data" => [
                'head' => $request->head,
                'desc' => $request->desc,
                'mtopic' => $request->mtopic,
                'action' => $request->action,
                'url' => $request->url,
            ],
            // "android" => [
            //     "priority" => "high"
            // ],
            // "apns" => [
            //     "headers" => [
            //         "apns-priority" => "10"
            //     ]
            // ]
        ]
    ];

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $accessToken,
        'Content-Type' => 'application/json'
    ])->post($url, $data);

    return $response->json();
}

function sendNotification(Request $request)
{
    $projectId = 'quickmart-af5b6'; // Change to your Firebase project ID
    $accessToken = $request->access_token; // Change to your service account private key
    if (!$accessToken) {
        return ["error" => "Failed to retrieve access token"];
    }
        
    $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

    $data = [
        "message" => [
            "token" => $request->token,
            "notification" => [
                "title" => 'dcdcdcdcd',
                "body" => 'bgbgbgbgbgbg',
            ],
            // "data" => [
            //     'head' => $request->head,
            //     'desc' => $request->desc,
            //     'mtopic' => $request->mtopic,
            //     'action' => $request->action,
            //     'url' => $request->url,
            // ],
            // "android" => [
            //     "priority" => "high"
            // ],
            // "apns" => [
            //     "headers" => [
            //         "apns-priority" => "10"
            //     ]
            // ]
        ]
    ];

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $accessToken,
        'Content-Type' => 'application/json'
    ])->post($url, $data);

    return $response->json();
}
}
