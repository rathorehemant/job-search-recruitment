<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*Permissions*/

        $permissions = [

            [
                'name' => 'View Dashboard',
                'slug' => 'dashboard.view',
            ],

            [
                'name' => 'View Users',
                'slug' => 'users.view',
            ],

            [
                'name' => 'Create Users',
                'slug' => 'users.create',
            ],

            [
                'name' => 'Update Users',
                'slug' => 'users.update',
            ],

            [
                'name' => 'Delete Users',
                'slug' => 'users.delete',
            ],

            [
                'name' => 'View Leads',
                'slug' => 'leads.view',
            ],

            [
                'name' => 'Create Leads',
                'slug' => 'leads.create',
            ],

            [
                'name' => 'Update Leads',
                'slug' => 'leads.update',
            ],

            [
                'name' => 'Delete Leads',
                'slug' => 'leads.delete',
            ],

            [
                'name' => 'View Customers',
                'slug' => 'customers.view',
            ],

            [
                'name' => 'View Roles',
                'slug' => 'roles.view',
            ],

            [
                'name' => 'Create Roles',
                'slug' => 'roles.create',
            ],

            [
                'name' => 'Update Roles',
                'slug' => 'roles.update',
            ],

            [
                'name' => 'Delete Roles',
                'slug' => 'roles.delete',
            ],
        ];


        /*Create / Update Permissions*/

        $permissionModels = [];

        foreach ($permissions as $permission) {

            $permissionModels[$permission['slug']] =
                Permission::updateOrCreate(
                    [
                        'slug' => $permission['slug'],
                    ],
                    [
                        'name' => $permission['name'],
                    ]
                );
        }


        /* Admin Role*/

        $admin = Role::updateOrCreate(
            [
                'name' => 'Admin',
            ]
        );


        $admin->permissions()->sync(
            collect($permissionModels)
                ->pluck('id')
                ->values()
                ->toArray()
        );


       /*sale s user role and permissions*/

        $salesUser = Role::updateOrCreate(
            [
                'name' => 'Sales User',
            ]
        );


        $salesPermissions = [
            'dashboard.view',
            'leads.view',
            'leads.create',
            'leads.update',
            'customers.view',
        ];


        $salesUser->permissions()->sync(
            collect($permissionModels)
                ->only($salesPermissions)
                ->pluck('id')
                ->values()
                ->toArray()
        );
    }
}