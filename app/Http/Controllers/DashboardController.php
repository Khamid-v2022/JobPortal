<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends BaseController
{

    public function index(){
        $title = "Dashboard";

        return view('dashboard', [
            'title' => $title,
            'user' => $this->user
        ]);
    }
}
