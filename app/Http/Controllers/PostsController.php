<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Http\Requests\Posts\CreatePostsRequest;
use App\Http\Requests\Posts\UpdatePostsRequest;
//use App\Http\Controllers\Storage;

class PostsController extends Controller
{

    public function __construct(){
        $this->middleware('verifyCategoriesCount')->only(['create', 'store']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Post $posts)
    {
        //
        
        return view('posts.index')->with('posts',Post::all());
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //fetch all in category models
      
        return view('posts.create')->with('categories', Category::all())->with('tags', Tag::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePostsRequest $request, Post $post)
    {
        
       //dd($request->all());
        //upload the image
       $image = $request->image->store('posts');

           
        //create the post
        $post = Post::create([

            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'image' => $image,
            'published_at' => $request->published_at,
            'category_id' => $request->category,
            'user_id' => auth()->user()->id

        ]);

        if ($request->tags){
            $post->tags()->attach($request->tags);
        }

        // //flash message

        // //redirect users

        return redirect(route('posts.index'))->with('status', 'Post created successfully');

       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
        return view('posts.create')->with('post', $post)->with('categories', Category::all())->with('tags', Tag::all());

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostsRequest $request,Post $post)
    {
        $data = $request->only(['title', 'description', 'published_at', 'content']);
        
        //check if new image
        if ($request->hasFile('image')){
            // update it
            $image = $request->image->store('posts');
            // delete old one
            $post->deleteImage();
            $data['image'] = $image;
        }
 
        if($request->tags){
            $post->tags()->sync($request->tags);
        }

        // update attributes
        $post->update($data);
        //redirect user
        return redirect(route('posts.index'))->with('status', 'Post updated successfully');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        

      $post = Post::withTrashed()->where('id',$id)->firstOrFail();

       if ($post->trashed()){
        $post->deleteImage();
           $post->forceDelete();
       }
       else {
        $post->delete();
       }
        
        return redirect(route('posts.index'))->with('status', 'Post deleted successfully');

    }

    //list of trash post
    public function trashed(){

        $trashed= Post::withTrashed()->whereNotNull('deleted_at')->get();

        return view('posts.index')->with('posts', $trashed);

    }

       //list of trash post
       public function restore($id)
       {
           //
          // dd($id);
            $restore = Post::withTrashed()->where('id',$id)->firstOrFail();
           $restore->restore();
           return redirect(route('trashed-posts.index'))->with('status', 'Post restore successfully')->with('restore', $restore);
       }

}