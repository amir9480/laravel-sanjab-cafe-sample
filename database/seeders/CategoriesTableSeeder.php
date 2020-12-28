<?php

namespace Database\Seeders;

use App\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create(['name' => 'نوشیدنی']);
        Category::create(['name' => 'فست فود']);
        Category::create(['name' => 'صبحانه']);
    }
}
