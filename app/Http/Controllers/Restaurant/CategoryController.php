<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ManualPaymentRequest;
use App\Models\MenuCategory;
use App\Support\CurrentBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    use CurrentBranch;

    public function index()
    {
        $restaurant = $this->currentRestaurant();
        $branch     = $this->currentBranch();

        $categories = MenuCategory::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('branch_id', $branch->id)
            ->orderBy('sort_order')
            ->get();

        return view('restaurant.categories.index', compact('categories', 'branch'));
    }

    public function store(Request $request)
    {
        $restaurant = $this->currentRestaurant();
        $branch     = $this->currentBranch();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            // ...
        ]);

        MenuCategory::create([
            'restaurant_id' => $restaurant->id,
            'branch_id'     => $branch->id,
            'name'          => $data['name'],
            // ...
        ]);

        // ...
    }
}

