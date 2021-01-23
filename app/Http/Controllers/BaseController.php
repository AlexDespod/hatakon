<?php

namespace App\Http\Controllers;

use App\Classes\Parser;
use App\Models\Graph;

class BaseController extends Controller
{
    public function index(Parser $parser)
    {
        $parser = $parser->get_grechka();
    }
    public function graph()
    {
        $data = Graph::all();
        dd($data);
    }
}
