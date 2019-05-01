<?php

use Faker\Generator as Faker;

$factory->define(\JoseChan\Payment\Models\PaymentTypes::class, function (Faker $faker) {
    return [
        "name" => $faker->name,
        "status" => 1,
        "created_at" => date("Y-m-d H:i:s"),
        "updated_at" => date("Y-m-d H:i:s"),
    ];
});
