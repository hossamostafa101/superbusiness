<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\UserPlanPurchase;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
 public function index(Request $request)
{
    $user = $request->user();

    // ==========================
    // 1) بيانات المستخدم
    // ==========================
    $unreadNotificationsCount = 0;

    // نحمي نفسنا لو جدول notifications مش موجود أو العلاقة مش معرفة
    // if (
    //     Schema::hasTable('notifications') &&
    //     method_exists($user, 'unreadNotifications')
    // ) {
    //     $unreadNotificationsCount = $user->unreadNotifications()->count();
    // }

    $userData = [
        'id'   => $user->id,
        'name' => $user->name,
        'username' => $user->username ?? null,
        'email' => $user->email,
        'phone' => $user->phone ?? null,
        // 'unread_notifications_count' => $unreadNotificationsCount,
    ];

    // ==========================
    // 2) الرصيد (من user_plan_purchases)
    // ==========================
    $purchasesQuery = UserPlanPurchase::where('user_id', $user->id)
        ->where('status', 'paid');

    $invitesPurchasedTotal = (int) (clone $purchasesQuery)->sum('invitations_total');
    $invitesAvailable      = (int) (clone $purchasesQuery)->sum('invitations_remaining');
    $invitesUsedTotal      = max($invitesPurchasedTotal - $invitesAvailable, 0);

    // نجيب العملة من آخر عملية شراء مدفوعة، أو SAR كـ default
    $lastPurchaseCurrency = (clone $purchasesQuery)->latest('id')->value('currency');
    $currency = $lastPurchaseCurrency ?: 'SAR';

    $balance = [
        'invites_available'       => $invitesAvailable,
        'invites_purchased_total' => $invitesPurchasedTotal,
        'invites_used_total'      => $invitesUsedTotal,
        'currency'                => $currency,
    ];

    // ==========================
    // 3) الفلاتر (search + status + pagination)
    // ==========================
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

    // ==========================
    // 4) جلب المناسبات (events)
    // ==========================
    $today       = now()->toDateString();
    $appTimeZone = config('app.timezone', 'UTC');

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

    // فلتر البحث
    if (!empty($search)) {
        $eventsQuery->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('groom_name', 'like', "%{$search}%")
              ->orWhere('bride_name', 'like', "%{$search}%");
        });
    }

    // ترتيب المناسبات
    $eventsQuery->orderBy('event_date', 'desc')
                ->orderBy('created_at', 'desc');

    $paginator = $eventsQuery->paginate($perPage);

    // ==========================
    // 5) تحويل النتائج لفورمات الهوم
    // ==========================
    $eventsData = $paginator->getCollection()->map(function (Invitation $inv) use ($today, $appTimeZone) {

        // -------- date --------
        // event_date ممكن تكون Carbon أو string حسب الكاست
        if ($inv->event_date instanceof \Carbon\CarbonInterface) {
            $date = $inv->event_date->toDateString();
        } else {
            $date = $inv->event_date ? (string) $inv->event_date : null;
        }

        // -------- time + datetime_utc --------
        $rawTime     = $inv->event_time;
        $time        = null;
        $datetimeUtc = null;

        if (!empty($rawTime)) {
            $rawTime = trim((string) $rawTime);

            try {
                // لو القيمة فورمات وقت HH:MM أو HH:MM:SS
                if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $rawTime)) {
                    $time = substr($rawTime, 0, 5); // HH:MM
                }
                // لو القيمة فورمات تاريخ+وقت كامل Y-m-d H:i أو Y-m-d H:i:s
                elseif (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?$/', $rawTime)) {
                    $dt = Carbon::parse($rawTime, $appTimeZone);
                    $time = $dt->format('H:i');
                } else {
                    // أي فورمات تاني نحاول Carbon::parse مباشرة
                    $dt = Carbon::parse($rawTime, $appTimeZone);
                    $time = $dt->format('H:i');
                }
            } catch (\Throwable $e) {
                $time = null; // لو مش قادر يحلّلها نخليها null وما نكسرش الـ API
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
                $eventStatus = 'today'; // لو تحب تخليها 'published' بدالها عدّلها
            } else {
                $eventStatus = 'upcoming';
            }
        }

        $isToday = $date && $date === $today;

        return [
            'id'   => $inv->id,
            'name' => $inv->title,
            'image_url' => $inv->hero_media_url,
            'date' => $date,
            'time' => $time,
            'datetime_utc' => $datetimeUtc,
            'location_text' => $inv->venue_name,
            'label' => null,        // لو أضفت عمود label في الجدول عيّنه هنا
            'language' => 'ar',     // لو أضفت عمود language استبدلها
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
                'is_sample'        => false, // لو عندك عمود is_sample حطه هنا
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

    // ==========================
    // 6) الرد النهائي
    // ==========================
    return response()->json([
        'status'  => 'success',
        'message' => 'Home data loaded successfully',
        'user'    => $userData,
        'balance' => $balance,
        'filters' => $filters,
        'events'  => $events,
    ]);
}

}
