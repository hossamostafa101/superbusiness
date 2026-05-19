<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MyEventsController extends Controller
{
    /**
     * API لصفحة "مناسباتي / My Events"
     *
     * GET /api/my-events
     *
     * Query params:
     *  - search (اختياري)
     *  - status = all|upcoming|past|draft (اختياري, default: all)
     *  - page (اختياري, default: 1)
     *  - per_page (اختياري, default: 10)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // ============= الفلاتر =============
        $search  = $request->query('search');
        $status  = $request->query('status', 'all'); // all | upcoming | past | draft
        $perPage = (int) $request->query('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $filters = [
            'search' => $search ?? '',
            'status' => $status,
        ];

        $today       = now()->toDateString();
        $appTimeZone = config('app.timezone', 'UTC');

        // ============= بناء الكويري =============
        $eventsQuery = Invitation::query()
            ->where('user_id', $user->id)
            ->withCount([
                // إجمالي المدعوين
                'guests as invited_count',
                // عدد الموافقين
                'rsvps as accepted_count' => function ($q) {
                    $q->where('response', 'accepted');
                },
                // عدد المعتذرين
                'rsvps as declined_count' => function ($q) {
                    $q->where('response', 'declined');
                },
            ]);

        // فلتر الحالة
        if ($status === 'draft') {
            $eventsQuery->where('is_published', false);
        } elseif ($status === 'upcoming') {
            $eventsQuery
                ->where('is_published', true)
                ->whereDate('event_date', '>=', $today);
        } elseif ($status === 'past') {
            $eventsQuery
                ->where('is_published', true)
                ->whereDate('event_date', '<', $today);
        }

        // فلتر البحث بالعنوان/اسم العريس/العروسة
        if (!empty($search)) {
            $eventsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('groom_name', 'like', "%{$search}%")
                  ->orWhere('bride_name', 'like', "%{$search}%");
            });
        }

        // ترتيب: الأحدث في التاريخ، وبعدين الأحدث إنشاءً
        $eventsQuery->orderBy('event_date', 'desc')
                    ->orderBy('created_at', 'desc');

        $paginator = $eventsQuery->paginate($perPage);

        // ============= تحويل النتائج لفورمات الـ API =============
        $eventsData = $paginator->getCollection()->map(function (Invitation $inv) use ($today, $appTimeZone) {

            // -------- التاريخ (date) --------
            if ($inv->event_date instanceof \Carbon\CarbonInterface) {
                $date = $inv->event_date->toDateString();
            } else {
                $date = $inv->event_date ? (string) $inv->event_date : null;
            }

            // -------- الوقت (time) + datetime_utc --------
            $rawTime     = $inv->event_time;
            $time        = null;
            $datetimeUtc = null;

            if (!empty($rawTime)) {
                $rawTime = trim((string) $rawTime);

                try {
                    // فورمات HH:MM أو HH:MM:SS
                    if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $rawTime)) {
                        $time = substr($rawTime, 0, 5); // HH:MM
                    }
                    // فورمات Y-m-d H:i أو Y-m-d H:i:s
                    elseif (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?$/', $rawTime)) {
                        $dt   = Carbon::parse($rawTime, $appTimeZone);
                        $time = $dt->format('H:i');
                    } else {
                        // أي فورمات تانية نحاول مع Carbon مباشرة
                        $dt   = Carbon::parse($rawTime, $appTimeZone);
                        $time = $dt->format('H:i');
                    }
                } catch (\Throwable $e) {
                    $time = null; // لو فورمات غريب، نخليها null بدل ما نكسر الـ API
                }
            }

            if ($date && $time) {
                try {
                    $local = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $time, $appTimeZone);
                    $datetimeUtc = $local->utc()->toIso8601String();
                } catch (\Throwable $e) {
                    $datetimeUtc = null;
                }
            }

            // -------- إحصائيات المدعوين --------
            $invited  = (int) ($inv->invited_count ?? 0);
            $accepted = (int) ($inv->accepted_count ?? 0);
            $declined = (int) ($inv->declined_count ?? 0);
            $pending  = max($invited - $accepted - $declined, 0);

            // -------- حالة الحدث --------
            if (!$inv->is_published) {
                $eventStatus = 'draft';
            } else {
                if ($date && $date < $today) {
                    $eventStatus = 'past';
                } elseif ($date && $date === $today) {
                    $eventStatus = 'today'; // لو تحب تخليها 'published' عدّلها
                } else {
                    $eventStatus = 'upcoming';
                }
            }

            $isToday = $date && $date === $today;

            return [
                'id'   => $inv->id,
                'name' => $inv->title,
                'slug' => $inv->slug,
                'public_url' => url('/invite/' . $inv->slug), // عدّل المسار حسب الـ route العام عندك
                'image_url' => $inv->hero_media_url,
                'date' => $date,
                'time' => $time,
                'datetime_utc' => $datetimeUtc,
                'location_text' => $inv->venue_name,
                'label' => null,        // لو أضفت عمود label حطّه هنا
                'language' => 'ar',     // لو أضفت عمود language في الدعوة استخدمه
                'status' => $eventStatus,
                'stats' => [
                    'invited_count'  => $invited,
                    'accepted_count' => $accepted,
                    'declined_count' => $declined,
                    'pending_count'  => $pending,
                ],
                'flags' => [
                    'has_new_comments' => false, // لحد ما تعمل نظام تعليقات
                    'is_today'         => $isToday,
                    'is_sample'        => false, // لو أضفت عمود is_sample اربطه هنا
                ],
            ];
        })->values();

        $events = [
            'data' => $eventsData,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],
        ];

        return response()->json([
            'status'  => 'success',
            'message' => 'My events loaded successfully',
            'filters' => $filters,
            'events'  => $events,
        ]);
    }
}
