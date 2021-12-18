<?php

namespace Tests\Feature\Controllers\Admin;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use phpDocumentor\Reflection\Types\This;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $middlewares = ['web', 'auth:web', 'admin'];

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndexMethod()
    {
        $user = User::factory()->admin()->create();
        Post::factory()->count(100)->create();


        $this
            ->actingAs($user)
            ->get(\route('post.index'))
            ->assertOk()
            ->assertViewIs('admin.post.index')
            ->assertViewHas('posts', Post::latest()->paginate(15));

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }

    public function testCreateMethod()
    {
        $user = User::factory()->admin()->create();
        Tag::factory()->count(20)->create();

        $this
            ->actingAs($user)
            ->get(\route('post.create'))
            ->assertOk()
            ->assertViewIs('admin.post.create')
            ->assertViewHas('tags', Tag::latest()->get());

        $this
            ->assertEquals(
                request()->route()->middleware(),
                $this->middlewares
            );
    }

    public function testEditMethod()
    {

        $user = User::factory()->admin()->create();
        $post = Post::factory()->create();
        Tag::factory()->count(20)->create();


        $this
            ->actingAs($user)
            ->get(\route('post.edit', $post->id))
            ->assertOk()
            ->assertViewIs('admin.post.edit')
            ->assertViewHasAll(['post' => $post, 'tags' => Tag::latest()->get()]);

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }


    public function testStoreMethod()
    {

        $user = User::factory()->admin()->create();
        $post = Post::factory()->state(['user_id' => $user->id])->make()->toArray();
        $tags = Tag::factory()->count(\rand(2, 6))->create()->pluck('id')->toArray();

        $this
            ->actingAs($user)
            ->post(
                route('post.store'),
                array_merge($post, ['tags' => $tags])
            )
            ->assertSessionHas('message', 'new post has been created')
            ->assertRedirect(route('post.index'));


        $this->assertDatabaseHas('posts', $post);

        $this->assertEquals(
            $tags,
            Post::where($post)->first()->tags()->pluck('id')->toArray()
        );

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }

    public function testUpdateMethod()
    {

        $user = User::factory()->admin()->create();
        $post = Post::factory()->state(['user_id' => $user->id])->hasTags(\rand(2, 6))->create();
        $tags = Tag::factory()->count(\rand(2, 6))->create()->pluck('id')->toArray();

        $data =  Post::factory()->state(['user_id' => $user->id])->make()->toArray();

        $this
            ->actingAs($user)
            ->patch(
                route('post.update', $post->id),
                array_merge($data, ['tags' => $tags])
            )
            ->assertSessionHas('message', 'the post has been updated')
            ->assertRedirect(\route('post.index'));


        $this->assertDatabaseHas('posts', array_merge(['id' => $post->id], $data));

        $this->assertEquals(
            $tags,
            Post::where('id', $post->id)->first()->tags()->pluck('id')->toArray()
        );

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }


    public function testValidationRequiredData()
    {
        $user = User::factory()->admin()->create();
        $data = [];
        $errors = [
            'title' => 'The title field is required.',
            'tags' =>  'The tags field is required.',
            'image' => 'The image field is required.',
            'description' => 'The description field is required.',
        ];

        //store method
        $this
            ->actingAs($user)
            ->post(\route('post.store'), $data)
            ->assertSessionHasErrors($errors);

        //update method
        $this
            ->actingAs($user)
            ->post(\route('post.update', Post::factory()->create()->id), $data)
            ->assertSessionHasErrors($errors);
    }

    public function testValidationTagsHasArrayRule()
    {
        $user = User::factory()->admin()->create();
        $data = ['tags' => 0];
        $errors = [
            'tags' =>  'The tags must be an array.',
        ];

        //store method
        $this
            ->actingAs($user)
            ->post(\route('post.store'), $data)
            ->assertSessionHasErrors($errors);

        //update method
        $this
            ->actingAs($user)
            ->post(\route('post.update', Post::factory()->create()->id), $data)
            ->assertSessionHasErrors($errors);
    }

    public function testValidationTagsHasExistRule()
    {
        $user = User::factory()->admin()->create();
        $data = ['tags' => [0]];
        $errors = [
            'tags' =>  'The selected tags is invalid.',
        ];

        //store method
        $this
            ->actingAs($user)
            ->post(\route('post.store'), $data)
            ->assertSessionHasErrors($errors);

        //update method
        $this
            ->actingAs($user)
            ->post(\route('post.update', Post::factory()->create()->id), $data)
            ->assertSessionHasErrors($errors);
    }

    public function testValidationDescriptionHasMinRule()
    {
        $user = User::factory()->admin()->create();
        $data = ['description' => 'lorem'];
        $errors = [
            'description' =>  'The description must be at least 10 characters.',
        ];

        //store method
        $this
            ->actingAs($user)
            ->post(\route('post.store'), $data)
            ->assertSessionHasErrors($errors);

        //update method
        $this
            ->actingAs($user)
            ->post(\route('post.update', Post::factory()->create()->id), $data)
            ->assertSessionHasErrors($errors);
    }

    public function testValidationImageHasUrlRule()
    {
        $user = User::factory()->admin()->create();
        $data = ['image' => 'lorem'];
        $errors = [
            'image' =>  'The image must be a valid URL.',
        ];

        //store method
        $this
            ->actingAs($user)
            ->post(\route('post.store'), $data)
            ->assertSessionHasErrors($errors);

        //update method
        $this
            ->actingAs($user)
            ->post(\route('post.update', Post::factory()->create()->id), $data)
            ->assertSessionHasErrors($errors);
    }

    public function testDestroyMethod()
    {
        $post = Post::factory()
            ->hasTags(\rand(3, 10))
            ->hasComments(\rand(3, 10))
            ->create();

        $comments = $post->comments()->first()->toArray();

        $this
            ->actingAs(User::factory()->admin()->create())
            ->delete(\route('post.destroy', $post->id))
            ->assertSessionHasAll(['message' => 'The post has been deleted'])
            ->assertRedirect(\route('post.index'));

        $this
            ->assertDeleted($post)
            ->assertDatabaseMissing('comments', $comments)
            ->assertEmpty($post->tags);

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }
}
