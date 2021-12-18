<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Tag;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::latest()->paginate(15);
        return \view('admin.post.index', \compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tags = Tag::latest()->get();
        return \view('admin.post.create', \compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $post = \auth()->user()->posts()->create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'image' => $request->input('image')
        ]);


        $post->tags()->attach($request->tags);

        return redirect()->route('post.index')->with(['message' => 'new post has been created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $tags = Tag::latest()->get();

        return \view('admin.post.edit', \compact('tags', 'post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        $post->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'image' => $request->input('image')
        ]);


        $post->tags()->sync($request->tags);

        return redirect()->route('post.index')->with(['message' => 'the post has been updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->comments()->delete();
        $post->tags()->detach();
        $post->delete();

        return redirect(\route('post.index'))->with(['message' => 'The post has been deleted']);
    }
}
