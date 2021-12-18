<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class RegisterPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/register';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    public function submitForm(Browser $browser, array $data = [])
    {
        $browser
            ->type('name', $data['name'] ?? '')
            ->type('email', $data['email'] ?? '')
            ->type('password', $data['password'] ?? '')
            ->typeSlowly('password_confirmation', $data['password'] ?? '')
            ->click('@submitButton');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@submitButton' => 'form button[type="submit"]',
        ];
    }
}
