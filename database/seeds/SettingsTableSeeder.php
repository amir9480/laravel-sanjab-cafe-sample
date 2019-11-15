<?php

use Illuminate\Database\Seeder;

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
            ['min' => '0', 'max' => '20',  'point' => '1'],
            ['min' => '20', 'max' => '40', 'point' => '2'],
            ['min' => '40', 'max' => '50', 'point' => '3'],
            ['min' => '50', 'max' => '70', 'point' => '4'],
            ['min' => '70', 'max' => '90', 'point' => '5'],
            ['min' => '90', 'max' => '100', 'point' => '6'],
            ['min' => '100', 'max' => '1000000000', 'point' => '7'],
        ]);
    }
}
