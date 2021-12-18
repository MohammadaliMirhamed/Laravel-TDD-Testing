<?php

namespace Tests\Browser\Tests\Views\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Views\Auth\RegisterForm;
use Tests\DuskTestCase;

class RegisterFormTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testRegisterForm()
    {
        $user = User::factory()->make();

        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->visit(new RegisterForm)
                ->submitForm(
                    array_merge(
                        $user->toArray(),
                        ['password' => '123456789']
                    )
                )
                ->assertSee('Home Page')
                ->assertAuthenticatedAs(User::whereEmail($user->email)->first())
                ->assertPathIs('/')
                ->screenshot('screenshot')
                ->storeConsoleLog('log')
                ->storeSource('source');
        });
    }

    public function testRegisterFormValidation()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit(new RegisterForm)
                ->submitForm()
                ->assertSeeIn(
                    'input[name="name"] ~ .invalid-feedback',
                    'The name field is required.'
                )
                ->assertSeeIn(
                    'input[name="email"] ~ .invalid-feedback',
                    'The email field is required.'
                )
                ->assertSeeIn(
                    'input[name="password"] ~ .invalid-feedback',
                    'The password field is required.'
                )
                ->assertPathIs('/register');

            $data = User::factory()->make(['email' => 'this is not a valid email'])->toArray();
            $browser
                ->submitForm($data)
                ->assertSeeIn(
                    'input[name="email"] ~ .invalid-feedback',
                    'The email must be a valid email address.'
                );
        });
    }
}
