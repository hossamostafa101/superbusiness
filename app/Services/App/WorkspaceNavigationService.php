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

            'medical' => $this->medicalGroups($workspace),
            default => $this->generalGroups($workspace),
        };
    }

  private function restaurantGroups(Workspace $workspace): array
{
    $orderingMode = app(\App\Services\App\RestaurantMenu\RestaurantMenuSettingsService::class)
        ->values($workspace)['restaurant_ordering_mode'] ?? 'single_order';

    $ordersItem = $orderingMode === 'open_invoice'
        ? [
            'label' => 'جلسات الطاولات',
            'icon' => 'bi-journal-text',
            'route' => route('app.restaurant-menu.invoices.index', $workspace),
            'active' => request()->routeIs('app.restaurant-menu.invoices.*'),
        ]
        : [
            'label' => 'الطلبات',
            'icon' => 'bi-receipt',
            'route' => route('app.restaurant-menu.orders.index', $workspace),
            'active' => request()->routeIs('app.restaurant-menu.orders.*'),
        ];

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
            'title' => 'المطعم',
            'items' => [
                [
                    'label' => 'لوحة المطعم',
                    'icon' => 'bi-speedometer2',
                    'route' => route('app.restaurant-menu.dashboard', $workspace),
                    'active' => request()->routeIs('app.restaurant-menu.dashboard'),
                ],

                [
                    'label' => 'التشغيل',
                    'icon' => 'bi-grid-1x2',
                    'key' => 'restaurant-operations',
                    'active' => request()->routeIs([
                        'app.restaurant-menu.pos.*',
                        'app.restaurant-menu.orders.*',
                        'app.restaurant-menu.invoices.*',
                        'app.restaurant-menu.pos-shifts.*',
                    ]),
                    'children' => [
                        [
                            'label' => 'POS',
                            'icon' => 'bi-calculator',
                            'route' => route('app.restaurant-menu.pos.index', $workspace),
                            'active' => request()->routeIs('app.restaurant-menu.pos.*'),
                        ],
                        $ordersItem,
                        [
                            'label' => 'شيفتات POS',
                            'icon' => 'bi-clock-history',
                            'route' => route('app.restaurant-menu.pos-shifts.index', $workspace),
                            'active' => request()->routeIs('app.restaurant-menu.pos-shifts.*'),
                        ],
                    ],
                ],

                [
                    'label' => 'المنيو',
                    'icon' => 'bi-menu-button-wide',
                    'key' => 'restaurant-menu-manage',
                    'active' => request()->routeIs([
                        'app.restaurant-menu.categories.*',
                        'app.restaurant-menu.items.*',
                        'app.restaurant-menu.offers.*',
                        'app.restaurant-menu.theme.*',
                        'app.restaurant-menu.content-sections.*',
                    ]),
                    'children' => [
                        [
                            'label' => 'التصنيفات',
                            'icon' => 'bi-list-ul',
                            'route' => route('app.restaurant-menu.categories.index', $workspace),
                            'active' => request()->routeIs('app.restaurant-menu.categories.*'),
                        ],
                        [
                            'label' => 'الأصناف',
                            'icon' => 'bi-cup-hot',
                            'route' => route('app.restaurant-menu.items.index', $workspace),
                            'active' => request()->routeIs('app.restaurant-menu.items.*'),
                        ],
                        // [
                        //     'label' => 'العروض',
                        //     'icon' => 'bi-stars',
                        //     'route' => route('app.restaurant-menu.offers.index', $workspace),
                        //     'active' => request()->routeIs('app.restaurant-menu.offers.*'),
                        // ],
                        [
                            'label' => 'أقسام الصفحة',
                            'icon' => 'bi-layout-text-window-reverse',
                            'route' => route('app.restaurant-menu.content-sections.index', $workspace),
                            'active' => request()->routeIs('app.restaurant-menu.content-sections.*'),
                        ],
                        [
                            'label' => 'تصميم المنيو',
                            'icon' => 'bi-palette',
                            'route' => route('app.restaurant-menu.theme.edit', $workspace),
                            'active' => request()->routeIs('app.restaurant-menu.theme.*'),
                        ],
                        [
                            'label' => 'عرض المنيو',
                            'icon' => 'bi-box-arrow-up-left',
                            'route' => route('public.restaurant-menu.workspace', $workspace),
                            'active' => false,
                            'target' => '_blank',
                        ],
                    ],
                ],

                [
                    'label' => 'الفروع والطاولات',
                    'icon' => 'bi-building',
                    'key' => 'restaurant-branches-tables',
                    'active' => request()->routeIs([
                        'app.restaurant-menu.branches.*',
                        'app.restaurant-menu.tables.*',
                    ]),
                    'children' => [
                        [
                            'label' => 'الفروع',
                            'icon' => 'bi-shop',
                            'route' => route('app.restaurant-menu.branches.index', $workspace),
                            'active' => request()->routeIs('app.restaurant-menu.branches.*'),
                        ],
                        [
                            'label' => 'الطاولات و QR',
                            'icon' => 'bi-qr-code',
                            'route' => route('app.restaurant-menu.tables.index', $workspace),
                            'active' => request()->routeIs('app.restaurant-menu.tables.*'),
                        ],
                    ],
                ],

                [
                    'label' => 'الإعدادات',
                    'icon' => 'bi-gear',
                    'key' => 'restaurant-settings',
                    'active' => request()->routeIs([
                        'app.restaurant-menu.settings.*',
                        'app.restaurant-menu.payment-methods.*',
                        'app.restaurant-menu.staff.*',
                        'app.restaurant-menu.pos-settings.*',
                        'app.restaurant-menu.cash-registers.*',
                        'app.languages.*',
                        'app.links.*',
                    ]),
                    'children' => [
            
                        [
                            'label' => 'إعدادات المطعم',
                            'icon' => 'bi-sliders',
                            'route' => route('app.restaurant-menu.settings.edit', $workspace),
                            'active' => request()->routeIs('app.restaurant-menu.settings.*'),
                            'exists' => \Illuminate\Support\Facades\Route::has('app.restaurant-menu.settings.edit'),
                        ],
                        [
                            'label' => 'طرق الدفع',
                            'icon' => 'bi-credit-card',
                            'route' => route('app.restaurant-menu.payment-methods.index', $workspace),
                            'active' => request()->routeIs('app.restaurant-menu.payment-methods.*'),
                        ],
                        [
                            'label' => 'الموظفين',
                            'icon' => 'bi-person-badge',
                            'route' => route('app.restaurant-menu.staff.index', $workspace),
                            'active' => request()->routeIs('app.restaurant-menu.staff.*'),
                        ],
                        [
                            'label' => 'أدراج الكاش',
                            'icon' => 'bi-safe',
                            'route' => route('app.restaurant-menu.cash-registers.index', $workspace),
                            'active' => request()->routeIs('app.restaurant-menu.cash-registers.*'),
                        ],
                        [
                            'label' => 'إعدادات POS',
                            'icon' => 'bi-sliders2',
                            'route' => route('app.restaurant-menu.pos-settings.edit', $workspace),
                            'active' => request()->routeIs('app.restaurant-menu.pos-settings.*'),
                        ],
                        [
                            'label' => 'إعدادات PWA',
                            'icon' => 'bi-phone',
                            'route' => route('app.restaurant-menu.pwa-settings.edit', $workspace),
                            'active' => request()->routeIs('app.restaurant-menu.pwa-settings.*'),
                        ],
                        [
                            'label' => 'الروابط',
                            'icon' => 'bi-link-45deg',
                            'route' => route('app.links.index', $workspace),
                            'active' => request()->routeIs('app.links.*'),
                        ],
                        [
                            'label' => 'اللغات',
                            'icon' => 'bi-translate',
                            'route' => route('app.languages.index', $workspace),
                            'active' => request()->routeIs('app.languages.*'),
                        ],
                        [
    'label' => 'إعدادات الدليفري',
    'icon' => 'bi-scooter',
    'route' => route('app.restaurant-menu.delivery-settings.edit', $workspace),
    'active' => request()->routeIs('app.restaurant-menu.delivery-settings.*'),
],
[
    'label' => 'مناطق التوصيل',
    'icon' => 'bi-geo-alt',
    'route' => route('app.restaurant-menu.delivery-zones.index', $workspace),
    'active' => request()->routeIs('app.restaurant-menu.delivery-zones.*'),
],
[
    'label' => 'الدليفري',
    'icon' => 'bi-person-vcard',
    'route' => route('app.restaurant-menu.delivery-couriers.index', $workspace),
    'active' => request()->routeIs('app.restaurant-menu.delivery-couriers.*'),
],
                    ],
                ],
            ],
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

        // [
        //     'title' => 'النظام',
        //     'items' => [
        //         [
        //             'label' => 'الباقات والدفع',
        //             'icon' => 'bi-credit-card',
        //             'route' => route('billing.plans', $workspace),
        //             'active' => request()->routeIs('billing.*'),
        //         ],
        //     ],
        // ],
    ];
}
    private function restaurantGroupsX(Workspace $workspace): array
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
                'label' => 'طرق الدفع',
                'icon' => 'bi-credit-card',
                'route' => route('app.restaurant-menu.payment-methods.index', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.payment-methods.*'),
            ],
            [
                'label' => 'الموظفين',
                'icon' => 'bi-person-badge',
                'route' => route('app.restaurant-menu.staff.index', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.staff.*'),
            ],
            [
                'label' => 'إعدادات POS',
                'icon' => 'bi-sliders',
                'route' => route('app.restaurant-menu.pos-settings.edit', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.pos-settings.*'),
            ],

            [
                'label' => 'أدراج الكاش',
                'icon' => 'bi-safe',
                'route' => route('app.restaurant-menu.cash-registers.index', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.cash-registers.*'),
            ],
            [
                'label' => 'شيفتات POS',
                'icon' => 'bi-clock-history',
                'route' => route('app.restaurant-menu.pos-shifts.index', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.pos-shifts.*'),
            ],
            [
                'label' => 'POS',
                'icon' => 'bi-clock-history',
                'route' => route('app.restaurant-menu.pos.index', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.pos.*'),
            ],
            [
                'label' => 'الروابط',
                'icon' => 'bi-link-45deg',
                'route' => route('app.links.index', $workspace),
                'active' => request()->routeIs('app.links.*'),
            ],
            [
                'label' => 'إعدادات المنيو',
                'icon' => 'bi-gear',
                'route' => route('app.restaurant-menu.settings.edit', $workspace),
                'active' => request()->routeIs('app.restaurant-menu.settings.*'),
            ],
            [
                'label' => 'اللغات',
                'icon' => 'bi-translate',
                'route' => route('app.languages.index', $workspace),
                'active' => request()->routeIs('app.languages.*'),
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

                    [
                        'label' => 'الباقات والدفع',
                        'icon' => 'bi-credit-card',
                        'route' => route('billing.plans', $workspace),
                        'active' => request()->routeIs('billing.*'),
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

                    [

                        'label' => 'التصنيفات',
                        'icon' => 'bi-tags',
                        'route' => route('app.categories.index', $workspace),
                        'active' => request()->routeIs('app.categories.*'),
                    ],
                    [
                        'label' => 'المنتجات',
                        'icon' => 'bi-box-seam',
                        'route' => route('app.products.index', $workspace),
                        'active' => request()->routeIs('app.products.*'),
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








    private function medicalGroups(\App\Models\Workspace $workspace): array
    {
        return [
            [
                'title' => 'النظام الطبي',
                'items' => [
                    [
                        'label' => 'الرئيسية',
                        'icon' => 'bi-speedometer2',
                        'route' => route('app.medical.dashboard', $workspace),
                        'active' => request()->routeIs('app.medical.dashboard'),
                    ],
                    [
                        'label' => 'الفروع',
                        'icon' => 'bi-building',
                        'route' => route('app.medical.branches.index', $workspace),
                        'active' => request()->routeIs('app.medical.branches.*'),
                    ],
                    [
                        'label' => 'الإعدادات الطبية',
                        'icon' => 'bi-gear',
                        'route' => route('app.medical.settings.edit', $workspace),
                        'active' => request()->routeIs('app.medical.settings.*'),
                    ],
                    [
                        'label' => 'الأقسام',
                        'icon' => 'bi-grid',
                        'route' => route('app.medical.departments.index', $workspace),
                        'active' => request()->routeIs('app.medical.departments.*'),
                    ],
                    [
                        'label' => 'التخصصات',
                        'icon' => 'bi-award',
                        'route' => route('app.medical.specialties.index', $workspace),
                        'active' => request()->routeIs('app.medical.specialties.*'),
                    ],
                    [
                        'label' => 'الخدمات',
                        'icon' => 'bi-clipboard2-pulse',
                        'route' => route('app.medical.services.index', $workspace),
                        'active' => request()->routeIs('app.medical.services.*'),
                    ],
                    [
                        'label' => 'الفريق الطبي',
                        'icon' => 'bi-person-badge',
                        'route' => route('app.medical.staff.index', $workspace),
                        'active' => request()->routeIs('app.medical.staff.*'),
                    ],
                    [
                        'label' => 'المرضى',
                        'icon' => 'bi-people',
                        'route' => route('app.medical.patients.index', $workspace),
                        'active' => request()->routeIs('app.medical.patients.*'),
                    ],
                    [
                        'label' => 'الحجوزات',
                        'icon' => 'bi-calendar-check',
                        'route' => route('app.medical.appointments.index', $workspace),
                        'active' => request()->routeIs('app.medical.appointments.*'),
                    ],
                    [
                        'label' => 'لوحة اليوم',
                        'icon' => 'bi-kanban',
                        'route' => route('app.medical.appointments.board', $workspace),
                        'active' => request()->routeIs('app.medical.appointments.board'),
                    ],
                    [
                        'label' => 'الزيارات',
                        'icon' => 'bi-journal-medical',
                        'route' => route('app.medical.visits.index', $workspace),
                        'active' => request()->routeIs('app.medical.visits.*'),
                    ],
                    [
                        'label' => 'الروشتات',
                        'icon' => 'bi-capsule',
                        'route' => route('app.medical.prescriptions.index', $workspace),
                        'active' => request()->routeIs('app.medical.prescriptions.*'),
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
}
