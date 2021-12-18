<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class SingleController extends Controller
{
    public function index(Post $post)
    {
        $comments = $post
            ->comments()
            ->latest()
            ->paginate(15);

        return view('single', compact('post', 'comments'));
    }

    public function createComment(Request $request,Post $post)
    {
        $request->validate([
            'text' => 'required'
        ]);

        $post->comments()->create([
            'user_id' => auth()->user()->id,
            'text' => $request->input('text')
        ]);

        if( $request->ajax() )
            return ['created' => true];

        return redirect()->route('single',$post->id);
    }
}
