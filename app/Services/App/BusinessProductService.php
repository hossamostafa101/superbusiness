<?php

namespace App\Services\App;

use App\Models\BusinessProduct;
use App\Models\Workspace;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BusinessProductService
{
    public function create(
        Workspace $workspace,
        array $data,
        ?UploadedFile $image = null
    ): BusinessProduct {
        if ($image) {
            $data['image'] = $image->store('business/products', 'public');
        }

        return $workspace->businessProducts()->create([
            'category_id' => $data['category_id'] ?? null,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'] ?? null,
            'sale_price' => $data['sale_price'] ?? null,
            'currency' => $data['currency'] ?? 'EGP',
            'image' => $data['image'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_available' => $data['is_available'] ?? true,
            'is_featured' => $data['is_featured'] ?? false,
        ]);
    }

    public function update(
        BusinessProduct $product,
        array $data,
        ?UploadedFile $image = null
    ): BusinessProduct {
        if ($image) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $image->store('business/products', 'public');
        }

        $product->update([
            'category_id' => $data['category_id'] ?? null,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'] ?? null,
            'sale_price' => $data['sale_price'] ?? null,
            'currency' => $data['currency'] ?? 'EGP',
            'image' => $data['image'] ?? $product->image,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_available' => $data['is_available'] ?? false,
            'is_featured' => $data['is_featured'] ?? false,
        ]);

        return $product;
    }

    public function delete(BusinessProduct $product): void
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
    }
}