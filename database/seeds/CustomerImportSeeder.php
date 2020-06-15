<?php

use Illuminate\Database\Seeder;

class CustomerImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $rowCount = DB::table('customer_imports')->count();
      echo $rowCount . ' records in customer_imports table' . PHP_EOL;

      $deleted = DB::delete('delete from customer_imports');
      echo $deleted . ' rows deleted from customer_imports table' . PHP_EOL;

      $query = "LOAD DATA LOCAL INFILE 'E:/Dev/laravel-customer-management/storage/app/Customers.csv'
                  INTO TABLE customer_imports
                  FIELDS TERMINATED BY ','
                  OPTIONALLY ENCLOSED BY '\"'
                  LINES TERMINATED BY '\\n'
                  IGNORE 1 LINES";

      DB::connection()->getpdo()->exec($query);

      echo 'New file has been loaded to the customer_imports table' . PHP_EOL;
      $rowCount = DB::table('customer_imports')->count();
      echo $rowCount . ' records in customer_imports table' . PHP_EOL;    }
}
