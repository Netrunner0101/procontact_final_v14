<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomepageSmokeTest extends TestCase
{
    public function test_homepage_renders_landing_page(): void
    {
        $this->withoutVite();
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Simple, honest pricing', false);
        $response->assertSee('Loved by professionals', false);
        $response->assertSee('Frequently Asked Questions', false);
    }
}
