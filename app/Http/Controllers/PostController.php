<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class PostController extends Controller
{
    public function index() {

        $posts = Post::latest()->get();

        return Inertia::render('Post/Index', [
            'posts' => $posts
        ]);
    }

    public function create() {
        return Inertia::render('Post/Create');
    }

    public function store(Request $request) {

        $validated = $request->validate([
            'title' => 'required|string|min:3',
            'content' => 'required|string'
        ]);

        $post = Post::create($validated);

        if($post) {
            return Redirect::route('posts.index')->with('message', 'Data Berhasil Disimpan!');
        }
    }

    public function edit(Post $post){
        return Inertia::render('Post/Edit', [
            'post' => $post
        ]);
    }

    public function update(Request $request, Post $post){
        
        $validated = $request->validate([
            'title' => 'required|string|min:3',
            'content' => 'required|string'
        ]);

        $post->update($validated);

        if($post) {
            return Redirect::route('posts.index')->with('message', 'Data Berhasil Diupdate!');
        }
    }

    public function destroy($id) {

        $post = Post::findOrFail($id);

        $post->delete();

        if($post) {
            return Redirect::route('posts.index')->with('message', 'Data Berhasil Dihapus!');
        }
    }
}
