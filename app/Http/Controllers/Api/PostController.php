<?php

namespace App\Http\Controllers\Api;

//import model Post
use App\Models\Post;

use App\Http\Controllers\Controller;

//import resource PostResource
use App\Http\Resources\PostResource;

//import Http request
use Illuminate\Http\Request;
//import facade Storage
use Illuminate\Support\Facades\Storage;

//import facade Validator
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get all posts
        $posts = Post::latest()->paginate(5);

        //return collection of posts as a resource
        return new PostResource(true, 'List Data Posts', $posts);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'content'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        

        //create post

      
        $Token = JWTAuth::getToken();
        $cekdata = DB::select("SELECT * FROM users where remember_token = '$Token'");

        if(isset($cekdata)) {
            //return response JSON
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());
            $post = Post::create([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
            return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
        }

        //return response
        
    }
    public function show($id)
    {
        //find post by ID
        $post = Post::find($id);

        //return single post as a resource
        return new PostResource(true, 'Detail Data Post!', $post);
    }
    public function update(Request $request, $id)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //find post by ID
        $post = Post::find($id);

        //check if image is not empty
        if ($request->hasFile('image')) {

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts/' . basename($post->image));

            //update post with new image
            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        } else {

            //update post without image
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        }

        //return response
        return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
    }
    public function destroy($id)
    {

        //find post by ID
        $post = Post::find($id);
        
        // Storage::delete('public/posts/'.basename($post->image));

        
        $Token = JWTAuth::getToken();
        if($Token) {
            //return response JSON
            //delete post
            $post->delete();
             //delete image
            Storage::delete('public/posts/'.basename($post->image));
            return new PostResource(true, 'Data Post Berhasil Dihapus!', null);
        } 

        //return response
        
    }
}