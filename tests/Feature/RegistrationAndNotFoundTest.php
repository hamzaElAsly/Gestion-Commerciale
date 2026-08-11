<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegistrationAndNotFoundTest extends TestCase
{
    public function test_registration_routes_are_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register')->assertNotFound();
    }

    public function test_unknown_route_uses_the_custom_404_page(): void
    {
        $this->get('/cette-route-n-existe-pas')
            ->assertNotFound()
            ->assertSee('Page introuvable')
            ->assertSee('404');
    }
}
