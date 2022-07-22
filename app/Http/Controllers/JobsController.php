<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Job;
use App\Models\User;
use App\Models\Response;
use App\Models\Like;

class JobsController extends BaseController
{
    public function index(){
        $title = "Job List";
        $my_id = $this->user['id'];
        $jobs = Job::select('jobs.*', 'responses.id AS response_id', 'likes.id AS like_id')
                ->leftjoin('responses', function($q) use ($my_id) {
                    $q->on('jobs.id', '=', 'responses.job_id')
                    ->where('responses.user_id', '=', $my_id);
                })
                ->leftjoin('likes', function($q) use ($my_id) {
                    $q->on('jobs.id', '=', 'likes.like_target_id')
                    ->where('likes.user_id', '=', $my_id)
                    ->where('likes.like_type', '=', 'job');
                })
                ->orderBy('created_at', 'DESC')->get();

        return view('joblist', [
            'title' => $title,
            'user' => $this->user,
            'jobs' => $jobs
        ]);
    }


     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function response_job(Request $request){
        // check coin
        $user = User::where('id', $this->user['id'])->first();
        if($user->coin < env('RESPONSE_COIN')){
            return response()->json(['code'=>201, 'message'=>'Sorry. Not enough coins for response'], 200);
        }

        $user->coin--;
        $user->save();

        $response = Response::create([
            'user_id' => $this->user['id'],
            'job_id' => $request->job_id,
            'owner_id' => $request->owner_id,
            'message' => $request->message
        ]);

        return response()->json(['code'=>200, 'message'=>'', 'data' => $response], 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function like_job(Request $request){
        // check coin
        $like = Like::where('user_id', $this->user['id'])
        ->where('like_target_id', $request->like_target_id)
        ->where('like_type', 'job')->first();
        
        if($like){
            
            $user = Like::where('id', $like['id'])->delete();
        } else {
            Like::create([
                'user_id' => $this->user['id'],
                'like_target_id' => $request->like_target_id,
                'like_type' => 'job'
            ]);
        }

        return response()->json(['code'=>200, 'message'=>''], 200);
    }
}
