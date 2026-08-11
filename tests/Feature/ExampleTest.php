<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_guest_is_redirected_from_home_page(): void
    {
        $response = $this->get('/');

        $response->assertRedirect();
    }
}
