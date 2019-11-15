<?php

use App\User;
use Illuminate\Database\Seeder;
use Silber\Bouncer\Database\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! User::where('email', 'test@test.com')->exists()) {
            factory(User::class)->create(['email' => 'test@test.com', 'name' => 'sanjab', 'password' => bcrypt('123456')]);
            Artisan::call('sanjab:make:admin', ['--user' => 'test@test.com']);
        }

        if (! Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin', 'title' => 'ادمین']);
            Bouncer::allow('admin')->to('access_sanjab');

            foreach ([\App\User::class] as $model) {
                Bouncer::allow('admin')->toManage($model);
            }
            $admin = User::where('email', 'admin@test.com')->first();
            if ($admin == null) {
                $admin = factory(User::class)->create(['email' => 'admin@test.com', 'name' => 'nova', 'password' => bcrypt('123456')]);
            }
            Bouncer::assign('admin')->to($admin);
        }
    }
}
