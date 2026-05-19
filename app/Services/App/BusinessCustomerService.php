<?php

namespace App\Services\App;

use App\Models\BusinessCustomer;
use App\Models\Workspace;

class BusinessCustomerService
{
    public function create(Workspace $workspace, array $data): BusinessCustomer
    {
        return $workspace->businessCustomers()->create([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'gender' => $data['gender'] ?? null,
            'birthdate' => $data['birthdate'] ?? null,
            'source' => $data['source'] ?? 'manual',
            'status' => $data['status'] ?? 'active',
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function update(BusinessCustomer $customer, array $data): BusinessCustomer
    {
        $customer->update([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'gender' => $data['gender'] ?? null,
            'birthdate' => $data['birthdate'] ?? null,
            'source' => $data['source'] ?? 'manual',
            'status' => $data['status'] ?? 'active',
            'notes' => $data['notes'] ?? null,
        ]);

        return $customer;
    }

    public function delete(BusinessCustomer $customer): void
    {
        $customer->delete();
    }
}