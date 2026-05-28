<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Workspace extends Model
{
    protected $fillable = [
    'specification_id',
        'owner_id',
        'name',
        'slug',
        'type',
        'status',
        'trial_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'workspace_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    // public function subscriptions(): HasMany
    // {
    //     return $this->hasMany(Subscription::class);
    // }

    // public function activeSubscription()
    // {
    //     return $this->hasOne(Subscription::class)
    //         ->whereIn('status', ['trialing', 'active'])
    //         ->latestOfMany();
    // }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }


    public function businessProfile()
{
    return $this->hasOne(\App\Models\BusinessProfile::class);
}

public function businessLinks()
{
    return $this->hasMany(\App\Models\BusinessLink::class);
}

public function businessSettings()
{
    return $this->hasMany(\App\Models\BusinessSetting::class);
}

public function businessCategories()
{
    return $this->hasMany(\App\Models\BusinessCategory::class);
}

public function businessProducts()
{
    return $this->hasMany(\App\Models\BusinessProduct::class);
}

public function plans()
{
    return $this->hasMany(Plan::class);
}



public function subscriptions(): HasMany
{
    return $this->hasMany(\App\Models\Subscription::class);
}

public function activeSubscription(): HasOne
{
    return $this->hasOne(\App\Models\Subscription::class)
        ->whereIn('status', ['trialing', 'active', 'past_due'])
        ->where(function ($query) {
            $query->whereNull('ends_at')
                ->orWhere('ends_at', '>=', now());
        })
        ->latestOfMany();
}

public function currentSubscription(): HasOne
{
    return $this->activeSubscription();
}



public function businessEvents()
{
    return $this->hasMany(BusinessEvent::class);
}

public function businessLeads()
{
    return $this->hasMany(BusinessLead::class);
}


public function businessCustomers()
{
    return $this->hasMany(\App\Models\BusinessCustomer::class);
}

public function businessServices()
{
    return $this->hasMany(\App\Models\BusinessService::class);
}

public function businessAppointments()
{
    return $this->hasMany(\App\Models\BusinessAppointment::class);
}





public function getSetting(string $key, mixed $default = null): mixed
{
    return $this->businessSettings
        ->firstWhere('key', $key)
        ?->value ?? $default;
}
















public function restaurantBranches()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantBranch::class);
}

public function activeRestaurantBranches()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantBranch::class)
        ->where('is_active', true);
}

public function defaultRestaurantBranch()
{
    return $this->hasOne(\App\Models\RestaurantMenu\RestaurantBranch::class)
        ->where('is_default', true);
}

public function restaurantMenuCategories()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantMenuCategory::class);
}

public function restaurantMenuItems()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantMenuItem::class);
}

public function restaurantMenuSettings()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantMenuSetting::class);
}











public function restaurantItemVariants()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantItemVariant::class);
}

public function restaurantItemOptionGroups()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantItemOptionGroup::class);
}

public function restaurantItemOptions()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantItemOption::class);
}


public function branches()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantBranch::class);
}




public function businessRequests()
{
    return $this->hasMany(\App\Models\BusinessRequest::class);
}

public function restaurantOrders()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantOrder::class);
}

public function restaurantOrderItems()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantOrderItem::class);
}

public function restaurantOrderItemOptions()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantOrderItemOption::class);
}










public function restaurantTables()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantTable::class);
}










public function specification()
{
    return $this->belongsTo(\App\Models\Specification::class);
}

public function isSpecification(string $key): bool
{
    return $this->specification?->key === $key;
}

public function specificationKey(): string
{
    return $this->specification?->key ?? 'general';
}






public function restaurantInvoices()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantInvoice::class);
}

public function restaurantInvoiceGuests()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantInvoiceGuest::class);
}

public function restaurantInvoiceItems()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantInvoiceItem::class);
}

public function restaurantInvoiceItemOptions()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantInvoiceItemOption::class);
}


public function restaurantMenuThemeAssignment()
{
    return $this->hasOne(\App\Models\RestaurantMenu\RestaurantMenuThemeAssignment::class);
}












public function restaurantMenuContentSections()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantMenuContentSection::class);
}

public function restaurantMenuOffers()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantMenuOffer::class);
}


public function restaurantTableServiceRequests()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantTableServiceRequest::class);
}





public function languages()
{
    return $this->hasMany(\App\Models\WorkspaceLanguage::class);
}

public function activeLanguages()
{
    return $this->languages()
        ->where('is_active', true)
        ->orderByDesc('is_default')
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function translations()
{
    return $this->hasMany(\App\Models\WorkspaceTranslation::class);
}

public function defaultLanguage()
{
    return $this->hasOne(\App\Models\WorkspaceLanguage::class)
        ->where('is_default', true);
}













public function medicalSetting()
{
    return $this->hasOne(\Modules\Medical\Models\MedicalSetting::class);
}

public function medicalBranches()
{
    return $this->hasMany(\Modules\Medical\Models\MedicalBranch::class);
}

public function medicalDepartments()
{
    return $this->hasMany(\Modules\Medical\Models\MedicalDepartment::class);
}

public function medicalSpecialties()
{
    return $this->hasMany(\Modules\Medical\Models\MedicalSpecialty::class);
}

public function medicalServices()
{
    return $this->hasMany(\Modules\Medical\Models\MedicalService::class);
}

public function medicalStaff()
{
    return $this->hasMany(\Modules\Medical\Models\MedicalStaff::class);
}

public function medicalPatients()
{
    return $this->hasMany(\Modules\Medical\Models\MedicalPatient::class);
}








public function medicalStaffServices()
{
    return $this->hasMany(\Modules\Medical\Models\MedicalStaffService::class);
}






public function medicalStaffWorkingHours()
{
    return $this->hasMany(\Modules\Medical\Models\MedicalStaffWorkingHour::class);
}







public function medicalAppointments()
{
    return $this->hasMany(\Modules\Medical\Models\MedicalAppointment::class);
}



public function medicalVisits()
{
    return $this->hasMany(\Modules\Medical\Models\MedicalVisit::class);
}

public function medicalVisitNotes()
{
    return $this->hasMany(\Modules\Medical\Models\MedicalVisitNote::class);
}




public function medicalPrescriptions()
{
    return $this->hasMany(\Modules\Medical\Models\MedicalPrescription::class);
}

public function medicalPrescriptionItems()
{
    return $this->hasMany(\Modules\Medical\Models\MedicalPrescriptionItem::class);
}
}