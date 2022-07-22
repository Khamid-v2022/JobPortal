<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobsController extends BaseController
{
    public function index(){
        $title = "Job List";

        return view('joblist', [
            'title' => $title,
            'user' => $this->user
        ]);
    }
}
