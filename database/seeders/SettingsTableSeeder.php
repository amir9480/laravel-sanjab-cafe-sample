<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        set_setting('point.ranges', [
            ['min' => 0,      'max' => 20000,      'point' => 1],
            ['min' => 20000,  'max' => 40000,      'point' => 2],
            ['min' => 40000,  'max' => 50000,      'point' => 3],
            ['min' => 50000,  'max' => 70000,      'point' => 4],
            ['min' => 70000,  'max' => 90000,      'point' => 5],
            ['min' => 90000,  'max' => 100000,     'point' => 6],
            ['min' => 100000, 'max' => 2000000000, 'point' => 7],
        ]);

        set_setting('home.phone', '0111234567');
        set_setting('home.address', 'آمل, کیلومتر 5 جاده هراز, دانشگاه شمال, مرکز کارآفرینی شمال, طبقه اول, کافه خلاقیت');
        foreach (File::files(resource_path('images')) as $file) {
            File::copy($file, public_path("uploads/".$file->getFilename()));
        }
        set_setting('home.images', [
            'background1.jpeg',
            'background2.jpeg',
            'background3.jpeg',
            'background4.jpeg',
        ]);
    }
}
