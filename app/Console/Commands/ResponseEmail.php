<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Job;
use App\Models\User;
use App\Models\Response;
use Mail;
use App\Mail\NotifyMail;

use Carbon\Carbon;

class ResponseEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'response:emailing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $timestap_limit = Carbon::now()->subHour(env('RESPONSE_EMAIL_LIMIT_HOUR'));

        $jobs = Job::where('needs_email', '=', 'yes')->where('last_email_at', '<', $timestap_limit)->get(); 

        foreach($jobs as $job){
            // get owner email info
            $user = User::where('id', $job['user_id'])->first();

            $responsed_users = Response::select("responses.created_at", "users.name")
                                ->where('job_id', $job['id'])
                                ->leftjoin('users', 'responses.user_id', '=', 'users.id')
                                ->get();

            $mailData = [
                'job_title' => $job['title'],
                'users' => $responsed_users
            ]; 

            Mail::to($user['email'])->send(new NotifyMail($mailData));

            // update job status
            Job::where('id', $job['id'])->update(['last_email_at' => Carbon::now(), 'needs_email' => 'no']);
        }
    }
}
