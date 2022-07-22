<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Job;
use App\Models\User;
use App\Models\Response;
use App\Models\Like;

use Mail;
use App\Mail\NotifyMail;
use Carbon\Carbon;

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

        // create record
        $response = Response::create([
            'user_id' => $this->user['id'],
            'job_id' => $request->job_id,
            'owner_id' => $request->owner_id,
            'message' => $request->message
        ]);


        // Emailing
        $job_info = Job::where('id', $request->job_id)->first();
        $timestap_limit = Carbon::now()->subHour(env('RESPONSE_EMAIL_LIMIT_HOUR'));
        
        // email send condition - first response or passed 1 hour more since last emailing
        if(!$job_info['last_email_at'] || ($job_info['last_email_at'] && $job_info['last_email_at'] < $timestap_limit)){
            // get owner info
            $owner = User::where('id', $request->owner_id)->first();
            
            $responsed_users = Response::select("responses.created_at", "users.name")
                                ->where('job_id', $request->job_id)
                                ->leftjoin('users', 'responses.user_id', '=', 'users.id')
                                ->get();

            $mailData = [
                'job_title' => $job_info['title'],
                'users' => $responsed_users
            ]; 

            Mail::to($owner['email'])->send(new NotifyMail($mailData));
            
            // update job
            $job_info->last_email_at = Carbon::now();
            $job_info->needs_email = 'no';
            $job_info->save();

            // if (Mail::failures()) {
            //     return response()->json(['code'=>201, 'message'=>'Successfully send but failed email'], 200);
            // }else{
                return response()->json(['code'=>200, 'message'=>'Send an email'], 200);
            // }
        } else {
            $job_info->needs_email = 'yes';
            $job_info->save();
            
            return response()->json(['code'=>200, 'message'=>'Email will send in ' . env('RESPONSE_EMAIL_LIMIT_HOUR') . ' hour'], 200);
        }
        
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
