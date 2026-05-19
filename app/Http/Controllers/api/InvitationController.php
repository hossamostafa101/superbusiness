<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\UserPlanPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    /**
     * قائمة الدعوات الخاصة بالمستخدم الحالي
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Invitation::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at');

        // فلترة بسيطة اختيارية: ?status=published|draft
        if ($status = $request->query('status')) {
            if ($status === 'published') {
                $query->where('is_published', true);
            } elseif ($status === 'draft') {
                $query->where('is_published', false);
            }
        }

        // بحث بالعنوان أو اسم العروس/العريس: ?q=...
        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('groom_name', 'like', "%{$search}%")
                    ->orWhere('bride_name', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->query('per_page', 15);
        $invitations = $query->paginate($perPage);

        return response()->json([
            'data' => $invitations,
        ]);
    }

    /**
     * إنشاء دعوة جديدة
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'template_id'   => ['nullable', 'exists:invitation_templates,id'],
            'title'         => ['required', 'string', 'max:150'],
            'groom_name'    => ['nullable', 'string', 'max:120'],
            'bride_name'    => ['nullable', 'string', 'max:120'],
            'event_date'    => ['required', 'date'],
            'event_time'    => ['nullable', 'date_format:H:i'],
            'venue_name'    => ['nullable', 'string', 'max:200'],
            'location_url'  => ['nullable', 'url', 'max:500'],
            'location_lat'  => ['nullable', 'numeric'],
            'location_lng'  => ['nullable', 'numeric'],
            'hero_media_url' => ['nullable', 'url', 'max:500'],
            'message_text'  => ['nullable', 'string'],
            // لو ناوي تربطها مباشرة بشراء معين:
            'purchase_id'   => ['nullable', 'exists:user_plan_purchases,id'],
        ]);

        $invitation = new Invitation($validated);
        $invitation->user_id = $user->id;

        // الدعوة تبدأ Draft
        $invitation->is_published = false;
        $invitation->published_at = null;

        // إنشاء slug فريد
        $invitation->slug = $this->generateSlug($validated['title'] ?? null);

        $invitation->save();

        return response()->json([
            'message' => 'Invitation created',
            'data'    => $invitation,
        ], 201);
    }

    /**
     * عرض دعوة واحدة للمستخدم المالك
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $invitation = Invitation::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'data' => $invitation,
        ]);
    }

    /**
     * تحديث دعوة
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $invitation = Invitation::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'template_id'   => ['sometimes', 'nullable', 'exists:invitation_templates,id'],
            'title'         => ['sometimes', 'required', 'string', 'max:150'],
            'groom_name'    => ['sometimes', 'nullable', 'string', 'max:120'],
            'bride_name'    => ['sometimes', 'nullable', 'string', 'max:120'],
            'event_date'    => ['sometimes', 'required', 'date'],
            'event_time'    => ['sometimes', 'nullable', 'date_format:H:i'],
            'venue_name'    => ['sometimes', 'nullable', 'string', 'max:200'],
            'location_url'  => ['sometimes', 'nullable', 'url', 'max:500'],
            'location_lat'  => ['sometimes', 'nullable', 'numeric'],
            'location_lng'  => ['sometimes', 'nullable', 'numeric'],
            'hero_media_url' => ['sometimes', 'nullable', 'url', 'max:500'],
            'message_text'  => ['sometimes', 'nullable', 'string'],
            'purchase_id'   => ['sometimes', 'nullable', 'exists:user_plan_purchases,id'],
        ]);

        $invitation->fill($validated);

        // لو العنوان اتغيّر وعايز تغيّر الـ slug
        if (array_key_exists('title', $validated)) {
            $invitation->slug = $this->generateSlug($validated['title']);
        }

        $invitation->save();

        return response()->json([
            'message' => 'Invitation updated',
            'data'    => $invitation,
        ]);
    }

    /**
     * حذف دعوة
     * (لو حابب تستخدم SoftDeletes عدّل الموديل بعدين)
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $invitation = Invitation::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $invitation->delete();

        return response()->json([
            'message' => 'Invitation deleted',
        ]);
    }

    /**
     * نشر الدعوة (تصير متاحة عبر الرابط)
     */
    public function publish(Request $request, $id)
    {
        $user = $request->user();

        $invitation = Invitation::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        if (!$invitation->slug) {
            $invitation->slug = $this->generateSlug($invitation->title ?? null);
        }

        $invitation->is_published = true;
        $invitation->published_at = now();
        $invitation->save();

        return response()->json([
            'message' => 'Invitation published',
            'data'    => $invitation,
        ]);
    }

    /**
     * إلغاء نشر الدعوة
     */
    public function unpublish(Request $request, $id)
    {
        $user = $request->user();

        $invitation = Invitation::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $invitation->is_published = false;
        $invitation->published_at = null; // لو حابب تحفظ التاريخ السابق سيبها بدون تغيير
        $invitation->save();

        return response()->json([
            'message' => 'Invitation unpublished',
            'data'    => $invitation,
        ]);
    }

    /**
     * عرض الدعوة للـ landing page عن طريق الـ slug (بدون Auth)
     */
    public function publicShow($slug)
    {
        $invitation = Invitation::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // هنا بعدين نضيف إحصائيات + عدد الحضور .. إلخ لما نعمل Module الـ Guests/RSVP
        return response()->json([
            'data' => $invitation,
        ]);
    }

    /**
     * توليد slug فريد
     */
    protected function generateSlug(?string $title): string
    {
        $base = $title ? Str::slug($title) : Str::random(8);
        if ($base === '') {
            $base = Str::random(8);
        }

        $slug = $base;
        $i = 1;

        while (Invitation::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }



    public function addPage(Request $request)
    {
        $user = $request->user();

        // ==========================
        // 1) بيانات بسيطة عن المستخدم (اختياري)
        // ==========================
        $unreadNotificationsCount = 0;
        if (
            Schema::hasTable('notifications') &&
            method_exists($user, 'unreadNotifications')
        ) {
            $unreadNotificationsCount = $user->unreadNotifications()->count();
        }

        $userData = [
            'id'   => $user->id,
            'name' => $user->name,
            'username' => $user->username ?? null,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'unread_notifications_count' => $unreadNotificationsCount,
        ];

        // ==========================
        // 2) الرصيد (Balance)
        // ==========================
        $purchasesQuery = UserPlanPurchase::where('user_id', $user->id)
            ->where('status', 'paid');

        $invitesPurchasedTotal = (int) (clone $purchasesQuery)->sum('invitations_total');
        $invitesAvailable      = (int) (clone $purchasesQuery)->sum('invitations_remaining');
        $invitesUsedTotal      = max($invitesPurchasedTotal - $invitesAvailable, 0);

        $lastPurchaseCurrency = (clone $purchasesQuery)->latest('id')->value('currency');
        $currency = $lastPurchaseCurrency ?: 'SAR';

        $balance = [
            'invites_available'       => $invitesAvailable,
            'invites_purchased_total' => $invitesPurchasedTotal,
            'invites_used_total'      => $invitesUsedTotal,
            'currency'                => $currency,
        ];

        // ==========================
        // 3) قوالب الدعوات (Templates)
        // ==========================
        $templates = InvitationTemplate::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get([
                'id',
                'title',
                'preview_image_url',
                'template_json',
            ]);

        // ==========================
        // 4) قيم افتراضية للفورم (Defaults)
        // ==========================
        $defaults = [
            'event_date_default' => now()->toDateString(),  // تاريخ اليوم
            'event_time_default' => '20:00',                // 8 مساءً كمثال
            'timezone'           => config('app.timezone', 'UTC'),
            'language_default'   => 'ar',
        ];

        return response()->json([
            'status'   => 'success',
            'message'  => 'Add invitation page data loaded successfully',
            'user'     => $userData,
            'balance'  => $balance,
            'templates' => $templates,
            'defaults' => $defaults,
        ]);
    }




    public function detail(Request $request, $id)
{
    $user = $request->user();

    // نجيب الدعوة مع:
    // - template
    // - guests + rsvp لكل ضيف
    $invitation = Invitation::with([
            'template',
            'guests.rsvp',
        ])
        ->where('user_id', $user->id)
        ->where('id', $id)
        ->firstOrFail();

    // --------- التاريخ والوقت ----------
    if ($invitation->event_date instanceof \Carbon\CarbonInterface) {
        $date = $invitation->event_date->toDateString();
    } else {
        $date = $invitation->event_date ? (string) $invitation->event_date : null;
    }

    $rawTime = $invitation->event_time;
    $time    = null;
    if (!empty($rawTime)) {
        $rawTime = trim((string) $rawTime);
        // نحاول ناخد HH:MM
        if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $rawTime)) {
            $time = substr($rawTime, 0, 5);
        } elseif (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?$/', $rawTime)) {
            $time = substr($rawTime, 11, 5);
        }
    }

    // --------- إحصائيات المدعوين ----------
    $invitedCount  = 0;
    $acceptedCount = 0;
    $declinedCount = 0;

    foreach ($invitation->guests as $guest) {
        $invitedCount++;

        if ($guest->rsvp) {
            if ($guest->rsvp->response === 'accepted') {
                $acceptedCount++;
            } elseif ($guest->rsvp->response === 'declined') {
                $declinedCount++;
            }
        }
    }

    $pendingCount = max($invitedCount - $acceptedCount - $declinedCount, 0);

    // حالة الدعوة من ناحية التاريخ
    $today = now()->toDateString();
    if (! $invitation->is_published) {
        $status = 'draft';
    } else {
        if ($date && $date < $today) {
            $status = 'past';
        } elseif ($date && $date === $today) {
            $status = 'today';
        } else {
            $status = 'upcoming';
        }
    }

    // --------- بيانات القالب ----------
    $templateData = null;
    if ($invitation->template) {
        $templateData = [
            'id'               => $invitation->template->id,
            'title'            => $invitation->template->title,
            'preview_image_url'=> $invitation->template->preview_image_url,
            'config'           => $invitation->template->template_json, // لو عامل cast إلى array
        ];
    }

    // --------- قائمة الضيوف ----------
    $guestsData = $invitation->guests->map(function ($guest) {
        $rsvp = $guest->rsvp;

        return [
            'id'          => $guest->id,
            'full_name'   => $guest->full_name,
            'phone'       => $guest->phone_e164,
            'email'       => $guest->email,
            'tag'         => $guest->tag,
            'notes'       => $guest->notes,
            'invite_token'=> $guest->invite_token,
            'invite_url'  => $guest->invite_url, // لينك صفحة الويب /i/{token}
            'rsvp'        => $rsvp ? [
                'response'         => $rsvp->response,           // accepted | declined
                'companions_count' => (int) $rsvp->companions_count,
                'companions'       => $rsvp->companions_json,
                'message'          => $rsvp->message,
                'responded_at'     => optional($rsvp->responded_at)->toDateTimeString(),
            ] : null,
        ];
    })->values();

    // --------- بناء الرد ----------
    $data = [
        'invitation' => [
            'id'           => $invitation->id,
            'title'        => $invitation->title,
            'groom_name'   => $invitation->groom_name,
            'bride_name'   => $invitation->bride_name,
            'event_date'   => $date,
            'event_time'   => $time,
            'venue_name'   => $invitation->venue_name,
            'location_url' => $invitation->location_url,
            'hero_media_url' => $invitation->hero_media_url,
            'message_text' => $invitation->message_text,
            'slug'         => $invitation->slug,
            'is_published' => (bool) $invitation->is_published,
            'published_at' => optional($invitation->published_at)->toDateTimeString(),
            'status'       => $status,
            'template'     => $templateData,
            'stats'        => [
                'invited_count'  => $invitedCount,
                'accepted_count' => $acceptedCount,
                'declined_count' => $declinedCount,
                'pending_count'  => $pendingCount,
            ],
        ],
        'guests' => $guestsData,
    ];

    return response()->json([
        'status'  => 'success',
        'message' => 'Invitation details loaded successfully',
        'data'    => $data,
    ]);
}

}
