<?php

namespace App\Http\Controllers;

use App\Classes\Parser;
use App\Models\Graph;

class BaseController extends Controller
{
    public function index(Parser $parser)
    {

        // $data = $parser->get_grechka();
        $start = microtime(true);
        $top   = $parser->get_top_10_by_price();
        echo "done in : " . (microtime(true) - $start);
        dd($top);
    }
    public function graph()
    {
        $data = Graph::all();
        dd($data);
    }
}
