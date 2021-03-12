<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'image' => 'default.jpg',
            'verified' => User::UNVERIFIED_USER,
            'verification_token' => null,
            'admin' => User::REGULAR_USER,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            /**
             * Role assign for user
             */
            if ($user->id <= 1) {
                $userRoleAssign = [
                    'role_id' => 1,
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } else {
                $userRoleAssign = [
                    'role_id' => Role::where('id', '>', 1)->get()->random()->id,
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('roles_users')->insert($userRoleAssign);

            /**
             * Getting role information for user
             */
            $role = DB::table('roles_users')->where('user_id', $user->id)->first();

            /**
             * Updating User details based on role
             */
            $roleId = $role->role_id;

            if ($roleId == 1 || $roleId == 2) {
                $isAdmin = true;
            } else {
                $isAdmin = false;
            }

            $updateUser = User::findOrFail($user->id);
            $updateUser->email_verified_at = $isAdmin == true ? now() : null;
            $updateUser->verified = $isAdmin == true ? User::VERIFIED_USER : User::UNVERIFIED_USER;
            $updateUser->verification_token = $isAdmin == true ? null : User::generateVerificationCode();
            $updateUser->admin = $isAdmin == true ? User::ADMIN_USER : User::REGULAR_USER;
            $updateUser->save();
        });
    }
}
