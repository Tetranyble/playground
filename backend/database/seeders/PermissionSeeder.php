<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = collect(getModels())->map(function ($model) {
            $m = explode('\\', $model)[3];

            return [$m.' Create', $m.' View', $m.' Update', $m.' Delete',
                $m.' Store', $m.' Show', $m.' Index', $m.' Edit'];
        })->flatten()->map(function ($permission) {
            return Permission::factory()->create([
                'name' => Str::slug($permission, '_'),
                'label' => $permission,
            ]);
        });

        collect(config('backend.roles'))->each(function ($index, $role) use ($permissions) {
            $role = Role::create([
                'name' => Str::slug($role),
                'label' => Str::ucfirst($role),
                'description' => $index['description'],
                'order' => $index['order'],
                'is_system' => 1,
            ]);
            if ($role->name === 'admin') {
                $permissions->each(fn ($permission) => $role->givePermissionTo($permission));
            }

        });
    }
}
