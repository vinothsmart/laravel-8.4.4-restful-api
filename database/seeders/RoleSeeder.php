<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // Truncate the user table
        Role::truncate();

        // Clear the user events
        Role::flushEventListeners();

        // Add roles
        Role::factory()->create(['role' => 'SuperAdmin']);
        Role::factory()->create(['role' => 'Admin']);
        Role::factory()->create(['role' => 'User']);
    }
}
