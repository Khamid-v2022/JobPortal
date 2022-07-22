<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Job;
use Carbon\Carbon;

class MyjobController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $title = "My Job";
        $jobs = Job::where('user_id', $this->user['id'])->orderBy('created_at', 'DESC')->get();

        return view('myjob', [
            'title' => $title,
            'user' => $this->user,
            'jobs' => $jobs
        ]);
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
        //
        if(!isset($request->id)){
            // check can post or not 
            // user cannot post more than two job vacancies per 24 hours
            $limit_timestamp = Carbon::now()->subDay(env('LIMIT_DAY'));
            $jobs = Job::where('created_at', '>=', $limit_timestamp)->get();
            if(count($jobs) >= env('LIMIT_POST_COUNT')){
                return response()->json(['code'=>201, 'message'=>'You posted ' . env('LIMIT_POST_COUNT') . ' jobs within 24 hours.'], 200);
            }
        }

        $job = Job::updateOrCreate(['id' => $request->id], [
                'user_id' => $this->user['id'],
                'title' => $request->title,
                'description' => $request->description,
            ]);


        
        return response()->json(['code'=>200, 'message'=>'','data' => $job], 200);
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
        $user = Job::where('id', $id)->delete();
        return response()->json(['code'=>200, 'message'=>'Success'], 200);
    }
}
