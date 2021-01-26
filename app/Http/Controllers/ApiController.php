<?php

namespace App\Http\Controllers;

use App\Classes\Parser;
use App\Models\Graph;
use Exception;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    public function index(Parser $parser)
    {
        try {
            $data = $parser->get_all_grechka();
            return new JsonResponse(
                $data,
                200,
                ['Content-Type: application/json; charset=utf-8'],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        } catch (Exception $e) {
            return response($e->getMessage(), 500);
        }

    }
    public function graph()
    {
        try {
            $data = Graph::all();
            return new JsonResponse(
                $data,
                200,
                ['Content-Type: application/json; charset=utf-8'],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );

        } catch (Exception $e) {
            return response($e->getMessage(), 500);

        }

    }
    public function top(Parser $parser)
    {
        try {
            $data = $parser->get_top_10_by_price();
            return new JsonResponse(
                $data,
                200,
                ['Content-Type' => 'application/json; charset=utf-8'],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );

        } catch (Exception $e) {
            return response($e->getMessage(), 500);

        }

    }
}
