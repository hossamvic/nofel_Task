<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return response()->json(['data' => $posts], 200);  
    }

    public function create(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $imagePaths = [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('images', 'public');
            $imagePaths[] = $path;
        }
    }
    $post = new Post([
        'title' => $request->title,
        'images' => $imagePaths ? json_encode($imagePaths) : null,
    ]);

    $post->save();

    return response()->json(['message' => 'Post created successfully', 'data' => $post], 201);
    }

    public function edit(Request $request, $id)
    {
        $post = Post::find($id);
    
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }
    
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
    
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('images', 'public');
                $imagePaths[] = $path;
            }
        }
        $post->title = $request->title;
        $post->images = $imagePaths ? json_encode($imagePaths) : null;
        $post->save();
    
        return response()->json(['message' => 'Post updated successfully', 'data' => $post], 200);
    }

    public function destroy( $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}
