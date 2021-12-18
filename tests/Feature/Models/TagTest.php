<?php

namespace Tests\Feature\Models;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Tests\Feature\Helpers\ModelHelpersTesting;

class TagTest extends TestCase
{
    use RefreshDatabase, ModelHelpersTesting;

    protected function model(): Model
    {
        return new Tag;
    }

    public function testTagRelationshipWithPost()
    {
        $post_count = \rand(2, 20);

        $tag = Tag::factory()
            ->has(Post::factory()->count($post_count))
            ->create();

        $this->assertCount($post_count, $tag->posts);
        $this->assertTrue($tag->posts->first() instanceof Post);
    }
}
