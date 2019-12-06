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
        $this->call(UsersTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(CustomersTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(CartsTableSeeder::class);
    }
}
