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

class JobsApiController extends BaseController
{

    // For API
    public function get_jobs(Request $request){
        $request->validate([
            'filter' => 'required',
        ]);

        if($request->filter == 'date')
            $order_by = 'created_at';
        else if($request->filter == 'count')
            $order_by = 'responses_count';


        $jobs = Job::withCount('responses')->orderBy($order_by, "DESC")->get();

        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);

    }

    public function find_job($id){
        $job = Job::where('id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => $job
        ]);
    }


    /*
    * user_id: poster user ID
    * title
    * description
    */
    
    public function create_job(Request $request){
        $request->validate([
            'user_id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        // check can post or not 
        // user cannot post more than two job vacancies per 24 hours
        $limit_timestamp = Carbon::now()->subDay(env('LIMIT_DAY'));
        $jobs = Job::where('user_id', $request->user_id)->where('created_at', '>=', $limit_timestamp)->get();
        if(count($jobs) >= env('LIMIT_POST_COUNT')){
            return response()->json(['code'=>201, 'message'=>'You posted ' . env('LIMIT_POST_COUNT') . ' jobs within 24 hours.'], 200);
        }

        // check coin
        // need 2 coin for post
        if($this->user['coin'] < env('POST_COIN')){
            return response()->json(['code'=>202, 'message'=>'Sorry. Not enough coins for post a job.'], 200);
        }

        
        $user = User::where('id', $request->user_id)->first();
        $user->coin = $user->coin - env('POST_COIN');
        $user->save();
      

        $job = Job::create([
                'user_id' => $request->user_id,
                'title' => $request->title,
                'description' => $request->description,
            ]);

        
        return response()->json(['code'=>200, 'message'=>'','data' => $job], 200);

    }

    /*
    * id: job id
    * user_id: poster user ID
    * title
    * description
    */
    public function update_job(Request $request){
        $request->validate([
            'id' => 'required|integer',
            'user_id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        $job = Job::where('id', $request->id)->where('user_id', $request->user_id)->first();
        
        if(!$job)
            return response()->json(['code'=>401, 'message'=>"You can't update other job post"], 200);
      

        Job::where('id', $request->id)->update([
                'user_id' => $request->user_id,
                'title' => $request->title,
                'description' => $request->description,
            ]);

        
        return response()->json(['code'=>200, 'message'=>'Success'], 200);

    }

     /*
    * id: job id
    * user_id: poster user ID
    */
    public function delete_job(Request $request){
        $request->validate([
            'id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        $job = Job::where('id', $request->id)->where('user_id', $request->user_id)->first();
        
        if(!$job)
            return response()->json(['code'=>401, 'message'=>"There is no job to delete"], 200);
      

        Job::where('id', $request->id)->delete();
        
        return response()->json(['code'=>200, 'message'=>'Success'], 200);

    }


    /*
    * user_id: response user id
    * job_id: target Job ID
    * message: content
    */
    public function send_response(Request  $request){
        $request->validate([
            'user_id' => 'required|integer',
            'job_id' => 'required|integer',
            'message' => 'required|string'
        ]);

         // check coin
        $user = User::where('id', $request->user_id)->first();
        if($user->coin < env('RESPONSE_COIN')){
            return response()->json(['code'=>401, 'message'=>'Sorry. Not enough coins for response'], 200);
        }
 
        $user->coin--;
        $user->save();
 

        $job_info = Job::where('id', $request->job_id)->first();
         // create record
         $response = Response::create([
             'user_id' => $request->user_id,
             'job_id' => $request->job_id,
             'owner_id' => $job_info['user_id'],
             'message' => $request->message
         ]);
 
 
        // Emailing
        $timestap_limit = Carbon::now()->subHour(env('RESPONSE_EMAIL_LIMIT_HOUR'));
         
        // email send condition - first response or passed 1 hour more since last emailing
        if(!$job_info['last_email_at'] || ($job_info['last_email_at'] && $job_info['last_email_at'] < $timestap_limit)){
            // get owner info
            $owner = User::where('id', $job_info['user_id'])->first();
             
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
 
             
            return response()->json(['code'=>200, 'message'=>'Send an email'], 200);
            
         } else {
             $job_info->needs_email = 'yes';
             $job_info->save();
             
             return response()->json(['code'=>200, 'message'=>'Email will send in ' . env('RESPONSE_EMAIL_LIMIT_HOUR') . ' hour'], 200);
         }
    }


    /*
    * user_id: request user id
    * response_id: target response ID
    */
    public function delete_response(Request  $request){
        $request->validate([
            'user_id' => 'required|integer',
            'response_id' => 'required|integer'
        ]);

        $exist = Response::where('user_id', $request->user_id)->where('id', $request->response_id)->first();
        if(!$exist){
            return response()->json(['code'=>401, 'message'=>"There is no response to delete"], 200);
        }

        Response::where('id', $exist['id'])->delete();
        
        // coin reward
        $user = User::where('id', $request->user_id)->first();
        $user->coin = $user->coin + env('RESPONSE_COIN');
        if( $user->coin > 5)
            $user->coin = 5;
        $user->save();

        return response()->json(['code'=>200, 'message'=>"Success"], 200);
    }

    /*
    * user_id: request user id
    */
    public function like_jobs(Request  $request){
        $request->validate([
            'user_id' => 'required|integer'
        ]);

        $jobs = Like::select("jobs.*")
            ->where('likes.user_id', $request->user_id)
            ->where('like_type', 'job')
            ->leftjoin('jobs', 'likes.like_target_id', '=', 'jobs.id')
            ->get();

        
        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
    }

}
