<?php

namespace Tests\Feature\Models;

use App\Helpers\DurationOfReading;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Tests\Feature\Helpers\ModelHelpersTesting;

class PostTest extends TestCase
{
    use RefreshDatabase, ModelHelpersTesting;

    protected function model(): Model
    {
        return new Post;
    }

    public  function testPostRelationshipWithUser()
    {
        $post = Post::factory()
            ->for(User::factory())
            ->create();

        $this->assertTrue(isset($post->user->id));
        $this->assertTrue($post->user instanceof User);
    }



    public function testPostRelationshipWithTags()
    {
        $tags_count = rand(2, 20);
        $post = Post::factory()
            ->has(Tag::factory()->count($tags_count))
            ->create();

        $this->assertCount($tags_count, $post->tags);
        $this->assertTrue($post->tags->first() instanceof Tag);
    }

    public function testPostRelationshipWithComments()
    {
        $comments_count = rand(2, 20);
        $post = Post::factory()
            ->has(Comment::factory()->count($comments_count))
            ->create();

        $this->assertCount($comments_count, $post->comments);
        $this->assertTrue($post->comments->first() instanceof Comment);
    }

    public function testDurationOfReadingAttribute()
    {
        $post = Post::factory()->make();

        $dor = new DurationOfReading();
        $dor->setText($post->description);

        $this->assertEquals($post->readingDuration, $dor->getDurationPerMin());
    }

    public function testDurationOfReadingAttributeWithMocking()
    {
        $post = Post::factory()->make();

        $mock = Mockery::mock(DurationOfReading::class);
        $mock
            ->shouldReceive('setText')
            ->with($post->description)
            ->once()
            ->andReturn($mock);

        $mock
            ->shouldReceive('getDurationPerMin')
            ->once()
            ->andReturn(20);

        $this->instance(DurationOfReading::class , $mock);

        $this->assertEquals(20 , $post->readingDuration);
    }
}
