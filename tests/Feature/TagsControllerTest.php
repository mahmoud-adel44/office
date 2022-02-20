<?php

namespace Tests\Feature;

use PHPUnit\Util\Test;
use Tests\TestCase;

class TagsControllerTest extends TestCase
{


    /**
     * @test
     */
    public function testItListTags(): void
    {
        $response = $this->get('/api/tags');

//        $response->assertStatus(200);
        $response->assertOk();

        $this->assertNotNull($response->json('data')[0]['id']);
    }

}
