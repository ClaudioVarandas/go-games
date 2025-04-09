<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_redirects_guests_to_home()
    {
        // Since '/dashboard' redirects to '/', guests should end up at '/'
        // which might then trigger the auth middleware redirect to '/login'
        // but the initial redirect from '/dashboard' itself goes to '/'
        $this->get('/dashboard')->assertRedirect('/');
    }

    public function test_dashboard_redirects_authenticated_users_to_home()
    {
        $this->actingAs($user = User::factory()->create());

        // Since '/dashboard' redirects to '/', authenticated users should also end up at '/'
        $this->get('/dashboard')->assertRedirect('/');
    }
}
