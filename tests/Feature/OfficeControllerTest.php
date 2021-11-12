<?php

namespace Tests\Feature;

use App\Models\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Util\Test;
use Tests\TestCase;

class OfficeControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_list_all_offices()
    {
        Office::factory(3)->create();

        $response = $this->get('/api/offices');

        $response->assertOk();

        $response->assertJsonCount(3, 'data');

        $this->assertNotNull($response->json('data')[0]['id']);

        //  $this->assertCount(3,$response->json('data'));
    }
}
