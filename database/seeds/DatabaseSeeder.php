<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      //$this->call(CustomerImportSeeder::class);
      $this->call(CustomerSeeder::class);
      //$this->call(ScheduleImportSeeder::class);
      //$this->call(ScheduleSeeder::class);
      //$this->call(CustomerClassificationSeeder::class);

    }
}
