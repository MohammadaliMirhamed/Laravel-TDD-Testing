<?php

namespace Tests\Feature\Controllers;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeControllersTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndexMethod()
    {
        Post::factory()->count(100)->create();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('home_page');
        $response->assertViewHas('posts', Post::latest()->paginate(15));
    }
}
