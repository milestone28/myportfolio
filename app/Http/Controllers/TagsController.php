<?php

namespace App\Http\Controllers;

use Illuminate\Http\TagsController\Request;
use App\Models\Tag;
use App\Http\Requests\Tags\CreateTagRequest;
use App\Http\Requests\Tags\UpdateTagsRequest;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Tag $tag)
    {
        //

        return view('tags.index')->with('tags',$tag::all()->sortDesc());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTagRequest $request,Tag $tag)
    {
        //
       

      Tag::create([
          
        'name' => $request->name
          
          ]);

      return redirect(route('tags.index'))->with('status', 'Tag created successfully!');

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
    public function edit(Tag $tag)
    {
        //
        return view('tags.create')->with('tag', $tag);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTagsRequest $request, Tag $tag)
    {
        //


        $Tag->update([
            'name' => $request->name
        ]);

        return redirect(route('tags.index'))->with('status', 'Tag updated successfully!');;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        //
        if($tag->posts->count() > 0){
            session()->flash('error', 'Tag cannot be deleted, because it is associated to some posts.');
            return redirect()->back();
        }
       
        $tag->delete();

        return redirect(route('tags.index'))->with('status', 'Tag deleted successfully!');

    }
}
