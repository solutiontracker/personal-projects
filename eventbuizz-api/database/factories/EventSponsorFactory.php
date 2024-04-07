<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\EventSponsor::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'event_id' => '22',
        'email' => $faker->unique()->safeEmail,
        'logo' => $faker->image(),
        'booth' => $faker->word,
        'phone_number' => $faker->phoneNumber,
        'website' => $faker->url,
        'twitter' => $faker->url,
        'facebook' => $faker->url,
        'linkedin' => $faker->url,
        'status' => $faker->boolean,
        'allow_card_reader' => $faker->boolean,
        'login_email' => $faker->email,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret,
    ];
});
