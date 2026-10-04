<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class UserService
{
    public function getAllUsers($request)
    {
        $users = User::query()
            ->with('role')
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->role, function ($query) use ($request) {
                $role = Role::where('name', $request->role)->first();

                if ($role) {
                    $query->where('role_id', $role->id);
                }
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return compact('users', 'roles');
    }

    public function getUserRoles($request)
    {
        try {

            return Role::query()
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString();
        } catch (\Exception $e) {

            throw new \Exception(
                'Failed to retrieve user roles: ' . $e->getMessage()
            );
        }
    }

    public function getPermissions()
    {
        return Permission::query()
            ->orderBy('name')
            ->get();
    }

    public function storeUserRoles($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name',
            ],
            'permissions' => [
                'nullable',
                'array',
            ],
            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();

        try {
            return DB::transaction(function () use ($data) {

                $role = Role::create([
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']),
                ]);

                $role->permissions()->sync(
                    $data['permissions'] ?? []
                );

                return $role->load('permissions');
            });
        } catch (\Exception $e) {

            \Log::error('Failed to create user role', [
                'exception' => $e,
                'name' => $data['name'] ?? null,
            ]);

            throw new \Exception(
                'Failed to create user role.'
            );
        }
    }


    public function updateUserRoles($request, $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name,' . $role->id,
            ],
            'permissions' => [
                'nullable',
                'array',
            ],
            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();

        try {
            return DB::transaction(function () use ($data, $role) {

                $role->update([
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']),
                ]);

                $role->permissions()->sync(
                    $data['permissions'] ?? []
                );

                return $role->fresh('permissions');
            });
        } catch (\Exception $e) {

            \Log::error('Failed to update user role', [
                'role_id' => $role->id,
                'exception' => $e,
            ]);

            throw new \Exception(
                'Failed to update user role.'
            );
        }
    }
}
