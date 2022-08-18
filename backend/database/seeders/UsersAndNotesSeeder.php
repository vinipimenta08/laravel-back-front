<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\RoleHierarchy;
use App\Models\UserClient;
use App\Models\Users;

class UsersAndNotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numberOfUsers = 10;
        $usersIds = array();
        $faker = Faker::create();
        /* Create roles */
        $rootRole = Role::create(['name' => 'root']);
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);


        RoleHierarchy::create([
            'role_id' => $rootRole->id,
            'hierarchy' => 1,
        ]);
        $userRoot = Users::create([
            'name' => 'root',
            'email' => 'root@root.com',
            'email_verified_at' => now(),
            'password' => bcrypt(env('DB_PASSWORD')), // password
            'menuroles' => 'root,admin,user',
            'id_client' => 1,
            'id_profile' => 1,
            'active' => 1
        ]);
        $userRoot->assignRole('root');
        $userRoot->assignRole('admin');
        $userRoot->assignRole('user');
        UserClient::create([
            'id_user' => $userRoot->id,
            'id_client' => 1
        ]);

        if (env('ENVIRONMENT') == 'DEV') {
            RoleHierarchy::create([
                'role_id' => $adminRole->id,
                'hierarchy' => 2,
            ]);
            RoleHierarchy::create([
                'role_id' => $userRole->id,
                'hierarchy' => 3,
            ]);
            $userAdmin = Users::create([
                'name' => 'admin',
                'email' => 'admin@admin.com',
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'menuroles' => 'admin,user',
                'id_client' => 2,
                'id_profile' => 2,
                'active' => 1
            ]);

            $userAdmin->assignRole('admin');
            $userAdmin->assignRole('user');
            UserClient::create([
                'id_user' => $userAdmin->id,
                'id_client' => 2
            ]);

            for($i = 0; $i<$numberOfUsers; $i++){
                $alternative = rand(0,1);
                $user = Users::create([
                    'name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'menuroles' => 'user',
                    'id_client' => 1,
                    'id_profile' => 3,
                    'active' => 1,
                    'alternative_profile' => $alternative
                ]);
                $user->assignRole('user');
                if ($alternative) {
                    UserClient::create([
                        'id_user' => $user->id,
                        'id_client' => 3
                    ]);
                    UserClient::create([
                        'id_user' => $user->id,
                        'id_client' => 1
                    ]);
                }
                array_push($usersIds, $user->id);
            }
        }



    }
}
