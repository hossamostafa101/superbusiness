<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserService
{
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $data['password'],
                'status' => $data['status'],
                'email_verified_at' => now(),
            ]);

            $this->syncAdminRoles($user, $data['roles'] ?? []);

            $user->adminProfile()->create([
                'job_title' => $data['job_title'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'status' => $data['status'],
            ];

            if (! empty($data['password'])) {
                $payload['password'] = $data['password'];
            }

            $user->update($payload);

            $this->syncAdminRoles($user, $data['roles'] ?? []);

            $user->adminProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'job_title' => $data['job_title'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]
            );

            return $user;
        });
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->syncRoles([]);
            $user->delete();
        });
    }

    public function toggleStatus(User $user): User
    {
        $user->update([
            'status' => $user->status === 'active' ? 'suspended' : 'active',
        ]);

        return $user;
    }

    private function syncAdminRoles(User $user, array $roleIds): void
    {
        $roles = Role::query()
            ->where('guard_name', 'admin')
            ->whereIn('id', $roleIds)
            ->pluck('name')
            ->toArray();

        $user->syncRoles($roles);
    }
}