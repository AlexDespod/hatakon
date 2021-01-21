<?php

namespace App\Http\Controllers;

use App\Classes\Metro;

class BaseController extends Controller
{
    public function index()
    {
        dd(Metro::get_grechka());
    }
}
