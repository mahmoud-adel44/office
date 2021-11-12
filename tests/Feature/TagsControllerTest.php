<?php

namespace Tests\Feature;

use PHPUnit\Util\Test;
use Tests\TestCase;

class TagsControllerTest extends TestCase
{


    /**
     * @test
     */
    public function itListTags()
    {
        $response = $this->get('/api/tags');

        $response->assertStatus(200);

        $this->assertNotNull($response->json('data')[0]['id']);
    }
}
