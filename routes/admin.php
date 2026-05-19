<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\Auth\AdminAuthController;
use App\Http\Controllers\admin\AdminHomeController;
use App\Http\Controllers\admin\AdminNotifyController;
use App\Http\Controllers\admin\FeatureController;
use App\Http\Controllers\admin\PaymentController;
use App\Http\Controllers\admin\PermissionController;
use App\Http\Controllers\admin\PlanController;
use App\Http\Controllers\admin\RestaurantMenuTemplateController;
use App\Http\Controllers\admin\RestaurantMenuTemplateSectionController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\SubscriptionController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\WorkspaceController;
use App\Http\Controllers\Billing\BillingController;

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::middleware('guest:admin')->group(function () {
            Route::get('login', [AdminAuthController::class, 'showLoginForm'])
                ->name('login');

            Route::post('login', [AdminAuthController::class, 'login'])
                ->name('login.post');
        });

        Route::post('logout', [AdminAuthController::class, 'logout'])
            ->middleware('auth:admin')
            ->name('logout');

        Route::middleware(['auth:admin', 'admin.access'])->group(function () {
            Route::get('/', [AdminHomeController::class, 'index'])
                ->name('dashboard');

            Route::get('dashboard', [AdminHomeController::class, 'index'])
                ->name('dashboard.index');

            Route::resource('users', UserController::class)->except(['show']);

            Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
                ->name('users.toggle-status');




            Route::resource('plans', PlanController::class)->except(['show']);

            Route::patch('plans/{plan}/toggle-status', [PlanController::class, 'toggleStatus'])
                ->name('plans.toggle-status');

            Route::resource('features', FeatureController::class)->except(['show']);

            Route::patch('features/{feature}/toggle-status', [FeatureController::class, 'toggleStatus'])
                ->name('features.toggle-status');


            Route::resource('workspaces', WorkspaceController::class)->except(['show']);

            Route::patch('workspaces/{workspace}/toggle-status', [WorkspaceController::class, 'toggleStatus'])
                ->name('workspaces.toggle-status');




            Route::resource('subscriptions', SubscriptionController::class)
                ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);


            Route::patch('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])
                ->name('subscriptions.cancel');

            Route::patch('subscriptions/{subscription}/mark-active', [SubscriptionController::class, 'markActive'])
                ->name('subscriptions.mark-active');

            Route::resource('payments', PaymentController::class)
                ->only(['index']);

            Route::get('payments/{payment}', [PaymentController::class, 'show'])
                ->name('payments.show');

            Route::post('payments/{payment}/approve', [PaymentController::class, 'approve'])
                ->name('payments.approve');

            Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])
                ->name('payments.reject');



            Route::middleware(['auth:web', 'workspace.access'])
                ->prefix('billing/{workspace:slug}')
                ->name('billing.')
                ->group(function () {
                    Route::get('plans', [BillingController::class, 'plans'])
                        ->name('plans');

                    Route::get('checkout/{plan:slug}', [BillingController::class, 'checkout'])
                        ->name('checkout');

                    Route::post('checkout/{plan:slug}', [BillingController::class, 'process'])
                        ->name('process');

                    Route::get('success', [BillingController::class, 'success'])
                        ->name('success');

                    Route::get('cancelled', [BillingController::class, 'cancelled'])
                        ->name('cancelled');
                });



Route::resource('restaurant-menu-templates', RestaurantMenuTemplateController::class);

Route::resource('restaurant-menu-template-sections', RestaurantMenuTemplateSectionController::class);






            Route::get('debug-permissions', function () {
                $user = auth('admin')->user();

                return [
                    'user_id' => $user?->id,
                    'email' => $user?->email,
                    'roles' => $user?->roles()->get(['id', 'name', 'guard_name']),
                    'permissions' => $user?->getAllPermissions()->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'guard_name' => $p->guard_name,
                    ])->values(),
                    'has_workspaces_view_admin' => in_array(
                        'workspaces.view',
                        $user?->getAllPermissions()
                            ->where('guard_name', 'admin')
                            ->pluck('name')
                            ->toArray() ?? [],
                        true
                    ),
                ];
            })->name('debug.permissions');

            // الأدوار والصلاحيات
            Route::resource('permissions', PermissionController::class)->only(['index', 'create', 'store', 'destroy']);
            Route::resource('roles', RoleController::class);



            // صفحة الفورم (تبويب: إلى Topic / إلى جهاز)
            Route::get('push-notifications', [AdminNotifyController::class, 'pushForm'])
                ->name('push_notifications.form');

            // إرسال إلى Topic
            Route::post('push-notifications/topic', [AdminNotifyController::class, 'sendNotificationToTopicPost'])
                ->name('push_notifications.topic');

            // إرسال إلى جهاز معيّن عن طريق FCM token
            Route::post('push-notifications/device', [AdminNotifyController::class, 'sendNotificationToDevicePost'])
                ->name('push_notifications.device');
        });
    });
