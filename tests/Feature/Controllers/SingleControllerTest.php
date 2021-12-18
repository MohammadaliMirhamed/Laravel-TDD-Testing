<?php

namespace Tests\Feature\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SingleControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndexMethod()
    {
        $post = Post::factory()
            ->has(Comment::factory()->count(rand(5,10)))
            ->create();

        $response = $this->get(route('single' , $post->id));

        $comments = $post->comments()->latest()->paginate(15);

        $response->assertStatus(200);
        $response->assertViewIs('single');
        $response->assertViewHasAll([
            'post' => $post,
            'comments' => $comments
        ]);

    }


    public function testCreateCommentMethodWhenUserLogin()
    {
        // $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $data = Comment::factory()->state([
            'user_id' => $user->id ,
            'commenttable_id' => $post->id
        ])->make()->toArray();

        $response = $this->actingAs($user)->post(
            route('single.comment' , $post->id),
            ['text' => $data['text']]
        );

        $response->assertRedirect(\route('single',$post->id));
        $this->assertDatabaseHas('comments',$data);

    }

    public function testCreateCommentMethodWhenUserLoginAjaxRequest()
    {
        // $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $data = Comment::factory()->state([
            'user_id' => $user->id ,
            'commenttable_id' => $post->id
        ])->make()->toArray();

        $response = $this
            ->actingAs($user)
            ->withHeaders([
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
            ])
            ->postJson(
                route('single.comment' , $post->id),
                ['text' => $data['text']]
            );

        $response
            ->assertOk()
            ->assertJson([
                'created' => true
            ]);

        $this->assertDatabaseHas('comments',$data);


    }

    public function testCreateCommentMethodWhenUserNotLogin()
    {
        $post = Post::factory()->create();

        $data = Comment::factory()->state([
            'commenttable_id' => $post->id
        ])->make()->toArray();

        unset($data['user_id']);

        $response = $this->post(
            route('single.comment' , $post->id),
            ['text' => $data['text']]
        );

        $response->assertRedirect(\route('login'));
        $this->assertDatabaseMissing('comments',$data);

    }

    public function testCreateCommentMethodWhenUserNotLoginAjaxRequest()
    {
        $post = Post::factory()->create();

        $data = Comment::factory()->state([
            'commenttable_id' => $post->id
        ])->make()->toArray();

        unset($data['user_id']);

        $response = $this
            ->withHeaders([
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
            ])
            ->postJson(
                route('single.comment' , $post->id),
                ['text' => $data['text']]
            );

        $response->assertUnauthorized();
        $this->assertDatabaseMissing('comments',$data);

    }

    public function testCreateCommentMethodRequestValidation()
    {
        $post = Post::factory()->create();

        $response = $this
            ->actingAs(User::factory()->create())
            ->withHeaders([
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
            ])
            ->postJson(
                route('single.comment' , $post->id),
                ['text' => '']
            );

        $response->assertJsonValidationErrors([
            'text'
        ]);

    }
}
