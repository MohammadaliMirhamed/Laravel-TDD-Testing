<?php

namespace Tests\Feature\Views;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LayoutViewTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testLayoutViewRenderdViewUserIsAdmin()
    {
        $user = User::factory()->state(['type' => 'admin'])->create();

        $this->actingAs($user);

        $view = $this->view('layouts.layout');

        $view->assertSee('<a href="/admin/dashboard">admin panel</a>' , false);

    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testLayoutViewRenderdViewUserIsNotAdmin()
    {
        $user = User::factory()->state(['type' => 'user'])->create();

        $this->actingAs($user);

        $view = $this->view('layouts.layout');

        $view->assertDontSee('<a href="/admin/dashboard">admin panel</a>' , false);

    }
}
