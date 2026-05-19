<?php

// app/Support/CurrentBranch.php
namespace App\Support;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

trait CurrentBranch
{
    protected function currentRestaurant()
    {
        $user = Auth::user();
        return $user?->restaurants()->first();
    }

    protected function currentBranch(): ?Branch
    {
        $branchId = session('current_branch_id');

        if (!$branchId) return null;

        $restaurant = $this->currentRestaurant();
        if (!$restaurant) return null;

        return $restaurant->branches()
            ->where('id', $branchId)
            ->first();
    }
}
