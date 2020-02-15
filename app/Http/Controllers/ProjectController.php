<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use Validator;

use function GuzzleHttp\Promise\all;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $project = Project::orderBy('id','DESC')->get();

        return response()->json($project);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $Roules = [
            'image' =>'required','image', 'mimes:jpg,jpeg,png,gif,svg', 'max:5000',
            'title' => 'required',
            'describe' => 'required',
        ];

        $validator = Validator::make($request->all(),$Roules);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()],401);
        }

        $photoUpload = $request->file('image');
        $photoName = time() . '.' . $photoUpload->getClientOriginalExtension();
        $photoPath = public_path('/images/Projects/');
        $photoUpload->move($photoPath,$photoName);
       
        $inpute = $request->all();
        $inpute['image'] = '/images/Projects/' . $photoName;
        $inpute['title'] = $request->title;
        $inpute['decribe'] = $request->describe;
        
        $project = Project::create($inpute);

        return response()->json($project,200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project = Project::find($id);

        if(is_null($project)){
            return response()->json('id not found', 404);
        }

        return response()->json($project,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $project = Project::find($id);

        if(is_null($project)){
            return response()->json('id not found!!!!', 404);
        }

        $project->update($request->all());

        return response()->json($project,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $project = Project::find($id);

        if(is_null($project)){
            return response()->json('id not found!!!!', 404);
        }

        $project->delete($request->all());

        return response()->json('recourd delete',200);
    }
}
