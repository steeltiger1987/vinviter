<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(AttributesTableSeeder::class);

        /* Seed countries */

        $rootPath = base_path();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::connection()->disableQueryLog();

        DB::table('events')->truncate();
        DB::table('images')->truncate();
        DB::table('countries')->truncate();
        DB::table('regions')->truncate();
        DB::table('cities')->truncate();
        DB::table('timezones')->truncate();

        DB::unprepared(File::get($rootPath.'/database/seeds/countries2.sql'));
        DB::unprepared(File::get($rootPath.'/database/seeds/regions2.sql'));
        DB::unprepared(File::get($rootPath.'/database/seeds/cities2.sql'));

        $timezones = [
            ['America/Los_Angeles', '(UTC-08:00) America, Los Angeles'],
            ['America/Chicago', '(UTC-06:00) America, Chicago'],
            ['America/Indiana/Knox', '(UTC-06:00) America, Indiana, Knox'],
            ['America/North_Dakota/Center', '(UTC-06:00) America, North Dakota, Center'],
            ['America/Detroit', '(UTC-05:00) America, Detroit'],
            ['America/Kentucky/Louisville', '(UTC-05:00) America, Kentucky, Louisville'],
            ['America/New_York', '(UTC-05:00) America, New York'],
            
            ['America/Vancouver', '(UTC-08:00) America, Vancouver'],
            ['America/Toronto', '(UTC-05:00) America, Toronto'],

            ['Europe/London', '(UTC) Europe, London'],
            ['Europe/Amsterdam', '(UTC+01:00) Europe, Amsterdam'],
            ['Europe/Berlin', '(UTC+01:00) Europe, Berlin'],
            ['Europe/Madrid', '(UTC+01:00) Europe, Madrid'],
            ['Europe/Paris', '(UTC+01:00) Europe, Paris'],
            ['Europe/Rome', '(UTC+01:00) Europe, Rome'],
        ];

        foreach($timezones as $timezone){
            $temp['php_timezone_identifier_name'] = $timezone[0];
            $temp['name'] = $timezone[1];

            $timezones_formatted[] = $temp;
        }

        DB::table('timezones')->insert($timezones_formatted);
    }
}
