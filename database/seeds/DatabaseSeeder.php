<?php

use Illuminate\Database\Seeder;
use JoseChan\Payment\Models\PaymentType as PaymentTypeModel;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

         $this->call([
             PaymentTypeConfigSeeder::class,
             PaymentTypeSeeder::class,
             MenuSeeder::class,
         ]);
    }
}
