<?php

namespace App\Http\Controllers;

class BaseController extends Controller
{
    public function index()
    {
        return view('welcome');
    }
    public function graph()
    {
        return view('graph');
    }
    public function top()
    {

    }
}
