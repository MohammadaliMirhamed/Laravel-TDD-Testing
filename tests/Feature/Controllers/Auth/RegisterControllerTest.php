<?php

namespace Tests\Feature\tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanRegister()
    {
        $data = User::factory()
            ->user()
            ->make(['email_verified_at' => null])
            ->toArray();

        $password = "123456789";

        Event::fake();

        $response = $this->post(route('register'), array_merge($data, [
            'password' => $password,
            'password_confirmation' => $password,
        ]));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', $data);

        Event::assertDispatched(Registered::class);
    }
}
