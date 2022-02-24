<?php

namespace Tests\Feature;

use App\Models\Office;
use App\Models\Reservation;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker as faker;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class OfficeControllerTest extends TestCase
{
    use RefreshDatabase, faker;

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

    /**
     * @test
     */
    public function itListsOfficesIncludingHiddenAndUnApprovedIfFilteringForTheCurrentLoggedInUser(): void
    {
        $user = User::factory()->create();

        Office::factory(3)->for($user)->create();

        Office::factory()->hidden()->for($user)->create();
        Office::factory()->pending()->for($user)->create();

        $this->actingAs($user);

        $response = $this->get('api/offices?user_id=' . $user->id);


        $response->assertOk()
            ->assertJsonCount(5, 'data');
    }
    /**
     * @test
     */
    public function itFiltersByUserId(): void
    {
        Office::factory(3)->create();

        $host = User::factory()->create();
        $office = Office::factory()->for($host)->create();

        $response = $this->get(
            'api/offices?user_id=' . $host->id
        );

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $office->id);
    }

    /**
     * @test
     */
    public function itFiltersByVisitorId(): void
    {
        Office::factory(3)->create();

        $user = User::factory()->create();
        $office = Office::factory()->create();

        Reservation::factory()->for(Office::factory())->create();
        Reservation::factory()->for($office)->for($user)->create();

        $response = $this->get(
            'api/offices?visitor_id=' . $user->id
        );

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $office->id);
    }

    /**
     * @test
     */
    public function itFiltersByTags(): void
    {
        $tags = Tag::factory(2)->create();

        $office = Office::factory()->hasAttached($tags)->create();
        Office::factory()->hasAttached($tags->first())->create();
        Office::factory()->create();

        $response = $this->get(
            'api/offices?'.http_build_query([
                'tags' => $tags->pluck('id')->toArray()
            ])
        );

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $office->id);
    }

    /**
     * @test
     */
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
        $this->assertEquals($user->id, $response->json('data.3.user.data.id'));
    }

    /**
     * @test
     */
    public function testReturnsNumbersOfReservations(): void
    {

        $office = Office::factory()->create();

        Reservation::factory()->for($office)->create(['status' => Reservation::STATUS_ACTIVE]);
        Reservation::factory()->for($office)->create(['status' => Reservation::STATUS_CANCELLED]);
        $response = $this->get('/api/offices');
        $response->assertOk();
        $this->assertEquals(1, $response->json('data')[3]['reservations_count']);
    }

    /**
     * @test
     */
    public function itOrderByDistanceIsProvided(): void
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

    /**
     * @test
     */
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

        $response = $this->get('/api/offices/' . $office->id);
        $this->assertisArray($response->json('data')['tags']);
        $this->assertisArray($response->json('data')['images']);
        $this->assertEquals($user->id, $response->json('data.user.data.id'));
    }

    /**
     * @test
     */
    public function itCreatesAnOffice(): void
    {
        $user = User::factory()->createQuietly();
//        $admin = User::factory()->create(['is_admin' => true]);
//        $user = User::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
//        $this->actingAs($user);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/offices', Office::factory(['approval_status' => Office::APPROVAL_PENDING])->raw([
            'tags' => [
                $tag1->id, $tag2->id
            ]
        ]));

        $response->assertCreated()
            ->assertJsonPath('data.approval_status', Office::APPROVAL_PENDING)
            ->assertJsonPath('data.reservations_count', 0)
//            ->assertJsonPath('data.user.data.id', $user->id)
            ->assertJsonCount(2, 'data.tags');
    }

    /**
     * @test
     */
    public function itUpdatesAnOffice(): void
    {
        $user = User::factory()->create();
        $tags = Tag::factory(2)->create();
        $office = Office::factory()
            ->for($user)
            ->create();

        $office->tags()->attach($tags);
        $this->actingAs($user);

        $response = $this->putJson('/api/offices/' . $office->id, [
            'title' => 'New Title',
            'description' => 'New Description',
            'tags' => $tags->pluck('id')->toArray()
        ]);

        $response->assertOk()
            ->assertJsonPath('data.title', 'New Title')
            ->assertJsonPath('data.description', 'New Description')
            ->assertJsonCount(2, 'data.tags');
    }

    /**
     * @test
     */
    public function itDoesntUpdateOfficeThatDoesntBelongToUser(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $office = Office::factory()->for($anotherUser)->create();

        $this->actingAs($user);

        $response = $this->putJson('api/offices/' . $office->id, [
            'title' => 'Amazing Office'
        ]);

        $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN);
    }

    /**
     * @test
     */
    public function itMarksTheOfficeAsPendingIfDirty(): void
    {
        $admin = User::factory()->create(['name' => 'mahmoud']);

//        Notification::fake();

        $user = User::factory()->create();
        $office = Office::factory()->for($user)->create();

        $this->actingAs($user);

        $response = $this->putJson('api/offices/' . $office->id, [
            'lat' => 40.74051727562952
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('offices', [
            'id' => $office->id,
            'approval_status' => Office::APPROVAL_PENDING,
        ]);

//        Notification::assertSentTo($admin, OfficePendingApproval::class);
    }

    /**
     * @test
     */
    public function itCanDeleteOffices(): void
    {
        Storage::put('/office_image.jpg', 'empty');

        $user = User::factory()->create();
        $office = Office::factory()->for($user)->create();

//        dd($office->reservations);

        $image = $office->images()->create([
            'path' => 'office_image.jpg'
        ]);

        $this->actingAs($user);

        $response = $this->deleteJson('api/offices/' . $office->id);

        $response->assertOk();

        $this->assertSoftDeleted($office);

        $this->assertModelMissing($image);

        Storage::assertMissing('office_image.jpg');
    }

    /**
     * @test
     */
    public function itCannotDeleteAnOfficeThatHasReservations(): void
    {
        $user = User::factory()->create();
        $office = Office::factory()->for($user)->create();

        Reservation::factory(3)->for($office)->create();

        $this->actingAs($user);

        $response = $this->deleteJson('api/offices/' . $office->id);

        $response->assertUnprocessable();

        $this->assertNotSoftDeleted($office);
    }

    /**
     * @test
     */
    public function itUpdatedTheFeaturedImageOfAnOffice(): void
    {
        $user = User::factory()->create();
        $office = Office::factory()->for($user)->create();

        $image = $office->images()->create([
            'path' => 'image.jpg'
        ]);

        $this->actingAs($user);

        $response = $this->putJson('api/offices/' . $office->id, [
            'featured_image_id' => $image->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.featured_image_id', $image->id);
    }

    /**
     * @test
     */
    public function itDoesntUpdateFeaturedImageThatBelongsToAnotherOffice(): void
    {
        $user = User::factory()->create();
        $office = Office::factory()->for($user)->create();
        $office2 = Office::factory()->for($user)->create();

        $image = $office2->images()->create([
            'path' => 'image.jpg'
        ]);

        $this->actingAs($user);

        $response = $this->putJson('api/offices/' . $office->id, [
            'featured_image_id' => $image->id,
        ]);

        $response->assertUnprocessable()->assertInvalid('featured_image_id');
    }

    protected function setUp(): void
    {
        parent::setUp();
        Office::factory(3)->create();
    }
}
