<?php

namespace App\Models\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantInvoiceItemOption extends Model
{
    protected $table = 'restaurant_invoice_item_options';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'invoice_id',
        'invoice_item_id',
        'option_group_id',
        'option_id',
        'group_name',
        'option_name',
        'price',
        'currency',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(RestaurantBranch::class, 'branch_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(RestaurantInvoice::class, 'invoice_id');
    }

    public function invoiceItem(): BelongsTo
    {
        return $this->belongsTo(RestaurantInvoiceItem::class, 'invoice_item_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(RestaurantItemOptionGroup::class, 'option_group_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(RestaurantItemOption::class, 'option_id');
    }
}