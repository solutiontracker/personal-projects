<?php

namespace Tests\Feature\RegistrationSite;

use App\Models\EventSponsor;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SponsorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * A basic test example.
     *
     * @return void
     */
    public function get_sponsors()
    {

        $this->withoutExceptionHandling();

//
//        $sponsors = factory(EventSponsor::class, 2)->create()->map(function ($sponsor) {
//            return $sponsor->only(['id', 'event_id', 'name', 'email', 'logo', 'booth', 'phone_number', 'website', 'twitter', 'facebook', 'linkedin', 'ribbons', 'allow_reservations', 'status', 'allow_card_reader']);
//        });
//        dump($sponsors);

        $response = $this->withHeaders([
            'event_id' => '2371',
            'language_id' => '1',
        ])->get('api/v2/sponsor/fetch/api-event');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'event_id',
                'name',
                'email',
                'logo',
                'booth',
                'phone_number',
                'website',
                'twitter',
                'facebook',
                'linkedin',
                'ribbons',
                'allow_reservations',
                'status',
                'allow_card_reader',
                'category'
            ]
        ]);
    }
}
