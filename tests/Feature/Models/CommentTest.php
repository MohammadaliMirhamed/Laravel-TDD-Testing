<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Tests\Feature\Helpers\ModelHelpersTesting;

class CommentTest extends TestCase
{
    use RefreshDatabase, ModelHelpersTesting;

    protected function model(): Model
    {
        return new Comment();
    }

    public function testCommentRelationshipWithPost()
    {
        $comment = Comment::factory()
            ->hasCommenttable(Post::factory())
            ->create();

        $this->assertTrue(isset($comment->commenttable->id));
        $this->assertTrue($comment->commenttable instanceof Post);
    }


    public function testCommentRelationshipWithUser()
    {
        $comment = Comment::factory()
            ->for(User::factory())
            ->create();

        $this->assertTrue(isset($comment->user->id));
        $this->assertTrue($comment->user instanceof User);
    }
}
