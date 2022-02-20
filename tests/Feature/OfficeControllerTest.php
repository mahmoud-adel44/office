<?php

namespace Tests\Feature;

use App\Models\Office;
use App\Models\Reservation;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker as faker;
use Tests\TestCase;

class OfficeControllerTest extends TestCase
{
    use RefreshDatabase , faker;

    protected function setUp(): void
    {
        parent::setUp();
        Office::factory(3)->create();
    }

    /**
     * @test
     */
    public function test_list_all_offices(): void
    {
        // will create an off by setUp Method
        $response = $this->get('/api/offices');
        $response->assertOk();
        $response->assertJsonCount(3, 'data');

        $this->assertNotNull($response->json('data')[0]['id']);
        $this->assertNotNull($response->json('meta'));
        $this->assertNotNull($response->json('links'));
    }

    public function test_only_lists_offices_that_are_not_hidden_and_approved(): void
    {
        // will create an off by setUp Method
        Office::factory(3)->create(['hidden' => TRUE]);
        Office::factory(3)
            ->create(
                ['approval_status' => Office::APPROVAL_PENDING]
            );
        $response = $this->get('/api/offices');
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    public function testFilterByHostId(): void
    {
        // will create an off by setUp Method
        $host = User::factory()->create();
        $office = Office::factory()->for($host)->create();
        $response = $this->get(
            '/api/offices?host_id=' . $host->id
        );
        $response->assertOk();
        $response->assertJsonCount(1, 'data');

        $this->assertEquals($office->id, $response->json('data.0.id'));
    }

    public function testFilterByUserId(): void
    {
        // will create an off by setUp Method
        $user = User::factory()->create();

        $office = Office::factory()->create();

        Reservation::factory()
            ->for(Office::factory())
            ->create();

        Reservation::factory()
            ->for($office)
            ->for($user)
            ->create();

        $response = $this->get(
            '/api/offices?user_id=' . $user->id
        );
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertEquals($office->id, $response->json('data')[0]['id']);
    }

    public function testIncludeUserAndTags(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();
        $office = Office::factory()
            ->for($user)
            ->create();

        $office->tags()->attach($tag);
        $office->images()->create([
            'path' => 'image.jpg',
        ]);

        $response = $this->get('/api/offices');
        $response->assertOk();
        $this->assertisArray($response->json('data')[0]['tags']);
        $this->assertisArray($response->json('data')[0]['images']);
        $this->assertEquals($user->id, $response->json('data')[3]['user']['id']);
    }

    public function testReturnsNumbersOfReservations(): void
    {

        $office = Office::factory()->create();

        Reservation::factory()->for($office)->create(['status' => Reservation::STATUS_ACTIVE]);
        Reservation::factory()->for($office)->create(['status' => Reservation::STATUS_CANCELLED]);
        $response = $this->get('/api/offices');
        $response->assertOk();
        $this->assertEquals(1, $response->json('data')[3]['reservations_count']);
    }


    public function testOrderByDistanceIsProvided(): void
    {
        Office::factory()->create([
            'lat' => '39.74051727562952',
            'lng' => '-8.770375324893696',
            'title' => 'Leiria'
        ]);

        Office::factory()->create([
            'lat' => '39.07753883078113',
            'lng' => '-9.281266331143293',
            'title' => 'Torres Vedras'
        ]);

        $response = $this->get('/api/offices?lat=38.720661384644046&lng=-9.16044783453807');
         $response->assertOk();
        $this->assertEquals('Torres Vedras', $response->json('data')[0]['title']);
        $this->assertEquals('Leiria', $response->json('data')[1]['title']);

        $response = $this->get('/api/offices');
        $response->assertOk();
        $this->assertEquals('Torres Vedras', $response->json('data')[4]['title']);
        $this->assertEquals('Leiria', $response->json('data')[3]['title']);

    }


    public function testItShowsTheOffice(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();
        $office = Office::factory()
            ->for($user)
            ->create();


        $office->tags()->attach($tag);
        $office->images()->create([
            'path' => 'image.jpg'
        ]);

        Reservation::factory()->for($office)->create(['status' => Reservation::STATUS_ACTIVE]);
        Reservation::factory()->for($office)->create(['status' => Reservation::STATUS_CANCELLED]);

        $response = $this->get('/api/offices/'.$office->id);
        $this->assertisArray($response->json('data')['tags']);
        $this->assertisArray($response->json('data')['images']);
        $this->assertEquals($user->id, $response->json('data.user.id'));
    }
}
