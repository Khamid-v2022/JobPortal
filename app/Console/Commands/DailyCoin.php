<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;

class DailyCoin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coin:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User receives 1 coin every 24 hours';

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
        $users = User::where('coin', '<', env('MAX_COIN_NUMBER'))->get();
        foreach($users as $user){
            User::where('id', $user['id'])->update(['coin' => (intval($user['coin']) + 1)]);
        }
    }
}
