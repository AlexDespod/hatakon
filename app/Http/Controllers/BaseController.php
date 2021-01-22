<?php

namespace App\Http\Controllers;

use App\Classes\Parser;

class BaseController extends Controller
{
    public function index()
    {
        $parser = (new Parser)->get_grechka();
    }
}
