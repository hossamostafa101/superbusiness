<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendDriverOrderReminders extends Command
{
    protected $signature = 'orders:driver-reminders';

    protected $description = 'إرسال تذكيرات للسائقين لو تأخروا في استلام الطلب أو توصيله';

    // نحقن FcmService في الكوماند
    public function __construct(protected FcmService $fcm)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->sendPickupReminders();
        $this->sendDeliveryReminders();
        $this->sendNoDriverReminders();

        return Command::SUCCESS;
    }

    protected function sendPickupReminders()
    {
        // 1) السائق قبل الطلب لكن ما استلموش من التاجر بعد 5 دقائق
        $query = Order::query()
            ->where('status', 'accepted')               // عدّل حسب حالتك
            ->whereNull('picked_at')
            ->whereNull('pickup_reminder_sent_at')
            ->where('accepted_at', '<=', now()->subMinutes(5))
            ->with(['driver']); // نفترض عندك علاقة driver() في Order

        $this->info('Pickup reminder: checking orders...');

        $query->chunkById(50, function ($orders) {
            foreach ($orders as $order) {
                if (! $order->driver || ! $order->driver->fcm_token) {
                    continue;
                }

                $this->info("Sending pickup reminder for order #{$order->id} to driver {$order->driver_id}");

                $this->sendFcmToToken(
                    $order->driver->fcm_token,
                    [
                        'action'   => 'pickup_reminder',
                        'order_id' => (string) $order->id,
                        'head'     => 'تذكير باستلام الطلب',
                        'desc'     => "لم تقم باستلام طلب رقم {$order->code} من التاجر بعد.",
                    ]
                );

                $order->pickup_reminder_sent_at = now();
                $order->save();
            }
        });
    }

    protected function sendDeliveryReminders()
    {
        // 2) السائق استلم (picked) لكن ما سلّم للعميل بعد X دقيقة
        $query = Order::query()
            ->whereIn('status', ['delivering', 'in_progress'])   // عدّل حسب النظام عندك
            ->whereNotNull('picked_at')
            ->whereNull('completed_at')
            ->whereNull('delivery_reminder_sent_at')
            ->where('picked_at', '<=', now()->subMinutes(20))    // مثلًا بعد 20 دقيقة
            ->with(['driver']);

        $this->info('Delivery reminder: checking orders...');

        $query->chunkById(50, function ($orders) {
            foreach ($orders as $order) {
                if (! $order->driver || ! $order->driver->fcm_token) {
                    continue;
                }

                $this->info("Sending delivery reminder for order #{$order->id} to driver {$order->driver_id}");

                $this->sendFcmToToken(
                    $order->driver->fcm_token,
                    [
                        'action'   => 'delivery_reminder',
                        'order_id' => (string) $order->id,
                        'head'     => 'تذكير بتسليم الطلب',
                        'desc'     => "لم تقم بتسليم طلب رقم {$order->code} للعميل بعد.",
                    ]
                );

                $order->delivery_reminder_sent_at = now();
                $order->save();
            }
        });
    }

    protected function sendNoDriverReminders()
    {
        $this->info('No-driver reminder: checking unassigned orders...');

        // 1) الطلبات اللي محدش قبلها بقالها 5 دقايق
        $orders = Order::query()
            ->where('status', 'pending')             // عدّل لو عندك اسم حالة مختلف
            ->whereNull('driver_id')
            ->whereNull('canceled_at')
            ->whereNull('no_driver_reminder_sent_at')
            ->where('created_at', '<=', now()->subMinutes(5))
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No unassigned orders need reminders.');
            return;
        }

        // 2) السائقين الفاضيين (ما عندهمش أوامر active)
        $drivers = User::query()
            ->where('type', 'driver')
            ->whereNotNull('fcm_token')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('driver_profiles')
                  ->whereColumn('driver_profiles.user_id', 'users.id')
                  ->where('driver_profiles.status', 'approved')
                  ->where('driver_profiles.is_active', true);
            })
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('orders')
                  ->whereColumn('orders.driver_id', 'users.id')
                  ->whereIn('orders.status', ['accepted', 'delivering', 'in_progress']);
            })
            ->get(['id', 'name', 'fcm_token']);

        if ($drivers->isEmpty()) {
            $this->info('No free drivers found to notify.');
            return;
        }

        // 3) إشعار لكل السائقين الفاضيين عن كل طلب pending متأخر
        foreach ($orders as $order) {

            $title = 'طلبات جديدة متاحة';
            $body  = "هناك طلب جديد رقم {$order->code} لم يقم أي سائق بقبوله بعد.";

            foreach ($drivers as $driver) {
                $token = $driver->fcm_token;
                if (! $token) {
                    continue;
                }

                $this->info("Sending no-driver reminder for order #{$order->id} to driver {$driver->id}");

                $this->sendFcmToToken($token, [
                    'action'   => 'unassigned_order_reminder',
                    'order_id' => (string) $order->id,
                    'head'     => $title,
                    'desc'     => $body,
                ]);
            }

            $order->no_driver_reminder_sent_at = now();
            $order->save();
        }
    }

    protected function sendFcmToToken(string $token, array $data): void
    {
        $ok = $this->fcm->sendToToken($token, $data);

        if (! $ok) {
            $this->error('FCM error while sending to token: ' . $token);
        }
    }
}
