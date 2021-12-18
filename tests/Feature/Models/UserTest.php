<?php

namespace Tests\Feature\Models;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Helpers\ModelHelpersTesting;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, ModelHelpersTesting;

    protected function model(): Model
    {
        return new User;
    }

    public function testUserRelationshipWithPost()
    {
        $postCount = rand(2, 20);

        $user = User::factory()
            ->has(Post::factory()->count($postCount))
            ->create();

        $this->assertCount($postCount, $user->posts);
        $this->assertTrue($user->posts->first() instanceof Post);
    }

    public function testUserRelationshipWithComment()
    {
        $commentCount = rand(2, 20);

        $user = User::factory()
            ->has(Comment::factory()->count($commentCount))
            ->create();

        $this->assertCount($commentCount, $user->comments);
        $this->assertTrue($user->comments->first() instanceof Comment);
    }
}
