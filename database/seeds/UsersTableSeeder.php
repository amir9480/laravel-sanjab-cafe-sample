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

        if (! Role::where('name', 'seller')->exists()) {
            Role::create(['name' => 'seller', 'title' => 'فروشنده']);
            Bouncer::allow('seller')->to('access_sanjab');
            Bouncer::allow('seller')->to('viewAny', \App\Customer::class);
            Bouncer::allow('seller')->to('view', \App\Customer::class);

            $seller = User::where('email', 'seller@test.com')->first();
            if ($seller == null) {
                $seller = factory(User::class)->create(['email' => 'seller@test.com', 'name' => 'seller', 'password' => bcrypt('123456')]);
            }
            Bouncer::assign('seller')->to($seller);
        }
    }
}
