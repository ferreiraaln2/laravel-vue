<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VueController extends Controller
{
    /**
     * Display the Vue application
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('app');
    }
}
