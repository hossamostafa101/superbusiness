<?php

namespace App\Services\App;

use App\Models\Workspace;

class WorkspaceNavigationService
{
    public function groups(Workspace $workspace): array
    {
        $workspace->loadMissing('specification');

        return match ($workspace->specificationKey()) {
            'restaurant' => $this->restaurantGroups($workspace),
            'appointments' => $this->appointmentsGroups($workspace),
            'bio' => $this->bioGroups($workspace),
            default => $this->generalGroups($workspace),
        };
    }

    private function restaurantGroups(Workspace $workspace): array
    {
        $orderingMode = app(\App\Services\App\RestaurantMenu\RestaurantMenuSettingsService::class)
            ->values($workspace)['restaurant_ordering_mode'] ?? 'single_order';

        $restaurantMenuItems = [
            [
                'label' => 'لوحة المطعم',
                'icon' => 'bi-speedometer2',
                'route' => route('app.restaurant-menu.dashboard', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.dashboard'),
            ],
            [
                'label' => 'الفروع',
                'icon' => 'bi-shop',
                'route' => route('app.restaurant-menu.branches.index', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.branches.*'),
            ],
            [
                'label' => 'تصنيفات المنيو',
                'icon' => 'bi-list-ul',
                'route' => route('app.restaurant-menu.categories.index', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.categories.*'),
            ],
            [
                'label' => 'أصناف المنيو',
                'icon' => 'bi-cup-hot',
                'route' => route('app.restaurant-menu.items.index', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.items.*'),
            ],
        ];

        if ($orderingMode === 'open_invoice') {
            $restaurantMenuItems[] = [
                'label' => 'جلسات الطاولات',
                'icon' => 'bi-journal-text',
                'route' => route('app.restaurant-menu.invoices.index', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.invoices.*'),
            ];
        } else {
            $restaurantMenuItems[] = [
                'label' => 'طلبات المنيو',
                'icon' => 'bi-receipt',
                'route' => route('app.restaurant-menu.orders.index', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.orders.*'),
            ];
        }

        $restaurantMenuItems = array_merge($restaurantMenuItems, [
            [
                'label' => 'الطاولات و QR',
                'icon' => 'bi-qr-code',
                'route' => route('app.restaurant-menu.tables.index', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.tables.*'),
            ],
            [
                'label' => 'تصميم المنيو',
                'icon' => 'bi-palette',
                'route' => route('app.restaurant-menu.theme.edit', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.theme.*'),
            ],
            [
                'label' => 'أقسام الصفحة',
                'icon' => 'bi-layout-text-window-reverse',
                'route' => route('app.restaurant-menu.content-sections.index', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.content-sections.*'),
            ],
            [
                'label' => 'إعدادات المنيو',
                'icon' => 'bi-gear',
                'route' => route('app.restaurant-menu.settings.edit', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.settings.*'),
            ],
            [
                'label' => 'عرض المنيو',
                'icon' => 'bi-box-arrow-up-left',
                'route' => route('public.restaurant-menu.workspace', $workspace),
                'active' => false,
                'target' => '_blank',
            ],
        ]);
        return [
            [
                'title' => 'الرئيسية',
                'items' => [
                    [
                        'label' => 'نظرة عامة',
                        'icon' => 'bi-speedometer2',
                        'route' => route('app.analytics.index', $workspace),
                        'active' => request()->routeIs('app.analytics.*'),
                    ],
                    [
                        'label' => 'بيانات المطعم',
                        'icon' => 'bi-shop-window',
                        'route' => route('app.business-profile.edit', $workspace),
                        'active' => request()->routeIs('app.business-profile.*'),
                    ],
                ],
            ],

            [

                'title' => 'منيو المطعم',
                'items' => $restaurantMenuItems,
                // [
                //     [
                //         'label' => 'الفروع',
                //         'icon' => 'bi-shop',
                //         'route' => route('app.restaurant-menu.branches.index', $workspace),
                //         'active' => request()->routeIs('app.restaurant-menu.branches.*'),
                //     ],
                //     [
                //         'label' => 'تصنيفات المنيو',
                //         'icon' => 'bi-list-ul',
                //         'route' => route('app.restaurant-menu.categories.index', $workspace),
                //         'active' => request()->routeIs('app.restaurant-menu.categories.*'),
                //     ],
                //     [
                //         'label' => 'أصناف المنيو',
                //         'icon' => 'bi-cup-hot',
                //         'route' => route('app.restaurant-menu.items.index', $workspace),
                //         'active' => request()->routeIs('app.restaurant-menu.items.*'),
                //     ],
                //     [
                //         'label' => 'طلبات المنيو',
                //         'icon' => 'bi-receipt',
                //         'route' => route('app.restaurant-menu.orders.index', $workspace),
                //         'active' => request()->routeIs('app.restaurant-menu.orders.*'),
                //     ],
                //     [
                //         'label' => 'الفواتير المفتوحة',
                //         'icon' => 'bi-journal-text',
                //         'route' => route('app.restaurant-menu.invoices.index', $workspace),
                //         'active' => request()->routeIs('app.restaurant-menu.invoices.*'),
                //     ],
                //     [
                //         'label' => 'الطاولات و QR',
                //         'icon' => 'bi-qr-code',
                //         'route' => route('app.restaurant-menu.tables.index', $workspace),
                //         'active' => request()->routeIs('app.restaurant-menu.tables.*'),
                //     ],  
                //     [
                //         'label' => 'إعدادات المنيو',
                //         'icon' => 'bi-gear',
                //         'route' => route('app.restaurant-menu.settings.edit', $workspace),
                //         'active' => request()->routeIs('app.restaurant-menu.settings.*'),
                //     ],
                //     [
                //         'label' => 'عرض المنيو',
                //         'icon' => 'bi-box-arrow-up-left',
                //         'route' => route('public.restaurant-menu.workspace', $workspace),
                //         'active' => false,
                //         'target' => '_blank',
                //     ],
                // ],
            ],

            [
                'title' => 'CRM',
                'items' => [
                    [
                        'label' => 'العملاء',
                        'icon' => 'bi-people',
                        'route' => route('app.customers.index', $workspace),
                        'active' => request()->routeIs('app.customers.*'),
                    ],
                    [
                        'label' => 'طلبات واستفسارات',
                        'icon' => 'bi-inbox',
                        'route' => $this->routeIfExists('app.requests.index', $workspace),
                        'active' => request()->routeIs('app.requests.*'),
                        'exists' => \Illuminate\Support\Facades\Route::has('app.requests.index'),
                    ],
                ],
            ],

            [
                'title' => 'النظام',
                'items' => [
                    [
                        'label' => 'الباقات والدفع',
                        'icon' => 'bi-credit-card',
                        'route' => route('billing.plans', $workspace),
                        'active' => request()->routeIs('billing.*'),
                    ],
                ],
            ],
        ];
    }

    private function appointmentsGroups(Workspace $workspace): array
    {
        return [
            [
                'title' => 'الحجوزات',
                'items' => [
                    [
                        'label' => 'نظرة عامة',
                        'icon' => 'bi-speedometer2',
                        'route' => route('app.analytics.index', $workspace),
                        'active' => request()->routeIs('app.analytics.*'),
                    ],
                    [
                        'label' => 'العملاء',
                        'icon' => 'bi-people',
                        'route' => route('app.customers.index', $workspace),
                        'active' => request()->routeIs('app.customers.*'),
                    ],
                    [
                        'label' => 'الخدمات',
                        'icon' => 'bi-briefcase',
                        'route' => route('app.services.index', $workspace),
                        'active' => request()->routeIs('app.services.*'),
                    ],
                    [
                        'label' => 'المواعيد',
                        'icon' => 'bi-calendar-check',
                        'route' => route('app.appointments.index', $workspace),
                        'active' => request()->routeIs('app.appointments.*'),
                    ],
                    [
                        'label' => 'إعدادات الحجز',
                        'icon' => 'bi-gear',
                        'route' => route('app.booking-settings.edit', $workspace),
                        'active' => request()->routeIs('app.booking-settings.*'),
                    ],
                ],
            ],
        ];
    }

    private function bioGroups(Workspace $workspace): array
    {
        return [
            [
                'title' => 'Digital Bio',
                'items' => [
                    [
                        'label' => 'بيانات الصفحة',
                        'icon' => 'bi-person-badge',
                        'route' => route('app.business-profile.edit', $workspace),
                        'active' => request()->routeIs('app.business-profile.*'),
                    ],
                    [
                        'label' => 'الروابط',
                        'icon' => 'bi-link-45deg',
                        'route' => route('app.links.index', $workspace),
                        'active' => request()->routeIs('app.links.*'),
                    ],
                ],
            ],
        ];
    }

    private function generalGroups(Workspace $workspace): array
    {
        return [
            [
                'title' => 'عام',
                'items' => [
                    [
                        'label' => 'نظرة عامة',
                        'icon' => 'bi-speedometer2',
                        'route' => route('app.analytics.index', $workspace),
                        'active' => request()->routeIs('app.analytics.*'),
                    ],
                    [
                        'label' => 'بيانات الصفحة',
                        'icon' => 'bi-person-badge',
                        'route' => route('app.business-profile.edit', $workspace),
                        'active' => request()->routeIs('app.business-profile.*'),
                    ],
                    [
                        'label' => 'الباقات والدفع',
                        'icon' => 'bi-credit-card',
                        'route' => route('billing.plans', $workspace),
                        'active' => request()->routeIs('billing.*'),
                    ],
                ],
            ],
        ];
    }

    private function routeIfExists(string $routeName, Workspace $workspace, string $fallback = '#'): string
    {
        if (! \Illuminate\Support\Facades\Route::has($routeName)) {
            return $fallback;
        }

        return route($routeName, $workspace);
    }
}
