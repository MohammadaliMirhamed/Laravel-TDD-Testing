<?php

namespace Tests\Feature\Controllers\Admin;

use App\Http\Middleware\UpdateUserActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->withoutMiddleware([UpdateUserActivity::class]);

        $user = User::factory()->create();

        Cache::shouldReceive('get')
            ->with('user' . $user->id . 'online')
            ->once()
            ->andReturn('online');

        $this
            ->actingAs(User::factory()->admin()->create())
            ->get(route('user.show', [$user['id']]))
            ->assertOk()
            ->assertViewIs('admin.user.show')
            ->assertViewHasAll([
                'user' => $user,
                'user_status' => 'online'
            ]);

        $this->assertDatabaseHas('users', $user->toArray());
    }
}
