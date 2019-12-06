<?php

use App\Category;
use App\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create(['name' => 'نوشابه', 'price' => 2000, 'category_id' => Category::where('name', 'نوشیدنی')->first()->id]);
        Product::create(['name' => 'دوغ', 'price' => 2000, 'category_id' => Category::where('name', 'نوشیدنی')->first()->id]);
        Product::create(['name' => 'دلستر', 'price' => 3000, 'category_id' => Category::where('name', 'نوشیدنی')->first()->id]);
        Product::create(['name' => 'آیسی مانکی', 'price' => 5000, 'category_id' => Category::where('name', 'نوشیدنی')->first()->id]);
        Product::create(['name' => 'رانی', 'price' => 5000, 'category_id' => Category::where('name', 'نوشیدنی')->first()->id]);

        Product::create(['name' => 'نیمرو', 'price' => 8000, 'category_id' => Category::where('name', 'صبحانه')->first()->id]);
        Product::create(['name' => 'املت', 'price' => 9000, 'category_id' => Category::where('name', 'صبحانه')->first()->id]);
        Product::create(['name' => 'کله پاچه', 'price' => 15000, 'category_id' => Category::where('name', 'صبحانه')->first()->id]);
        Product::create(['name' => 'سلف', 'price' => 15000, 'category_id' => Category::where('name', 'صبحانه')->first()->id]);

        Product::create(['name' => 'سوسیس بندری', 'price' => 12000, 'category_id' => Category::where('name', 'فست فود')->first()->id]);
        Product::create(['name' => 'فلافل', 'price' => 10000, 'category_id' => Category::where('name', 'فست فود')->first()->id]);
        Product::create(['name' => 'سیب زمینی', 'price' => 9000, 'category_id' => Category::where('name', 'فست فود')->first()->id]);
        Product::create(['name' => 'سیب زمینی پنیری', 'price' => 11000, 'category_id' => Category::where('name', 'فست فود')->first()->id]);
        Product::create(['name' => 'سیب زمینی قارچ و پنیر', 'price' => 13000, 'category_id' => Category::where('name', 'فست فود')->first()->id]);
        Product::create(['name' => 'سیب زمینی مخصوص', 'price' => 14000, 'category_id' => Category::where('name', 'فست فود')->first()->id]);
    }
}
