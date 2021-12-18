<?php

namespace Tests\Feature\Views;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SingleLayoutTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSingleLayoutViewWhenUserIsLoggedIn()
    {
        $post = Post::factory()->create();
        $comments = [];

        $requestView = (string) $this->actingAs(User::factory()->create())->view(
            'single',
            compact(['post', 'comments'])
        );

        $dom = new \DOMDocument();
        $dom->loadHTML($requestView);
        $dom = new \DOMXPath($dom);

        $action = \route('single.comment', $post->id);
        $this->assertCount(1, $dom->query("//form[@method='POST'][@action='$action']/textarea[@name='text']"));
    }

    public function testSingleLayoutViewWhenUserIsNotLoggedIn()
    {
        $post = Post::factory()->create();
        $comments = [];

        $requestView = (string) $this->view(
            'single',
            compact(['post', 'comments'])
        );

        $dom = new \DOMDocument();
        $dom->loadHTML($requestView);
        $dom = new \DOMXPath($dom);

        $action = \route('single.comment', $post->id);
        $this->assertCount(0, $dom->query("//form[@method='POST'][@action='$action']/textarea[@name='text']"));
    }
}
