<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Silber\Bouncer\Database\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
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

        set_setting('point.ranges', [
            ['min' => '0', 'max' => '20',  'point' => '1'],
            ['min' => '20', 'max' => '40', 'point' => '2'],
            ['min' => '40', 'max' => '50', 'point' => '3'],
            ['min' => '50', 'max' => '70', 'point' => '4'],
            ['min' => '70', 'max' => '90', 'point' => '5'],
            ['min' => '90', 'max' => '100', 'point' => '6'],
            ['min' => '100', 'max' => '1000', 'point' => '7'],
        ]);
    }
}
