<?php

namespace App\Http\Controllers\Api;

//import model Post
use App\Models\Post;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

//import resource PostResource
use App\Http\Resources\PostResource;

//import Http request
use Illuminate\Http\Request;
//import facade Storage
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get all posts
        $users = DB::select('CALL selectusers');

        //return collection of posts as a resource
        return new PostResource(true, 'List Data Posts', $users);
    }

    public function show($id)
    {
        //get all posts
        $users = DB::select("CALL searchbyname ('$id')");

        //return collection of posts as a resource
        return new PostResource(true, 'List Data Posts', $users);
    }
}