<?php

namespace Tests\Feature\Middlewares;

use App\Http\Middleware\UpdateUserActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UpdateUserActivityTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCheckTheActivityOfUserWhenIsLoggedIn()
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user);

        $request = Request::create('/', 'GET');

        $middleware = new UpdateUserActivity();

        $response = $middleware->handle($request, function () {
        });

        $this->assertNull($response);

        $this->assertEquals('online', Cache::get('user' . $user->id . 'online'));

        $this->travel(101)->seconds();

        $this->assertNull(Cache::get('user' . $user->id . 'online'));
    }

    public function testCheckTheActivityOfUserWhenIsNotLoggedIn()
    {
        $request = Request::create('/', 'GET');

        $middleware = new UpdateUserActivity();

        $response = $middleware->handle($request, function () {
        });

        $this->assertNull($response);
    }

    public function testCheckUpdateUserActivityMiddlewareIsInWebMiddlewareGroup()
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get(\route('home'))
            ->assertOk();

        $this->assertEquals('online', Cache::get('user' . $user->id . 'online'));
        $this->assertEquals(\request()->route()->middleware(), ['web']);
    }
}
