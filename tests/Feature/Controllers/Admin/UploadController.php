<?php

namespace Tests\Feature\Controllers\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UploadController extends TestCase
{
    use RefreshDatabase;

    protected $middlewares = ['web', 'auth:web', 'admin'];


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUploadMethod()
    {

        $image = UploadedFile::fake()->image('photo.png')->size(500);

        $this
            ->actingAs(User::factory()->admin()->create())
            ->withHeaders([
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
            ])
            ->postJson(\route('upload'), ['file' => $image])
            ->assertOk()
            ->assertJson(['url' => '/upload/' . $image->hashName()]);

        $this->assertFileExists(\public_path('/upload/' . $image->hashName()));

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }

    public function testUploadRequestHasImageRule()
    {

        $image = UploadedFile::fake()->create('file.txt');

        $this
            ->actingAs(User::factory()->admin()->create())
            ->withHeaders([
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
            ])
            ->postJson(\route('upload'), ['file' => $image])
            ->assertJsonValidationErrors([
                "file" => "The file must be an image."
            ]);

        $this->assertFileDoesNotExist(\public_path('/upload/' . $image->hashName()));

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }

    public function testUploadRequestHasSizeRule()
    {

        $image = UploadedFile::fake()->image('file.png')->size(200);

        $this
            ->actingAs(User::factory()->admin()->create())
            ->withHeaders([
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
            ])
            ->postJson(\route('upload'), ['file' => $image])
            ->assertJsonValidationErrors([
                "file" => "The file must be at least 250 kilobytes."
            ]);

        $this->assertFileDoesNotExist(\public_path('/upload/' . $image->hashName()));

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }

    public function testUploadRequestHasDimensionsRule()
    {

        $image = UploadedFile::fake()->image('file.png', 500, 500)->size(200);

        $this
            ->actingAs(User::factory()->admin()->create())
            ->withHeaders([
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
            ])
            ->postJson(\route('upload'), ['file' => $image])
            ->assertJsonValidationErrors([
                "file" => "The file has invalid image dimensions."
            ]);

        $this->assertFileDoesNotExist(\public_path('/upload/' . $image->hashName()));

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }
}
