<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * Class PingController
 * @package App\Http\Controllers\Api
 */
class PingController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => \Carbon\Carbon::now()
        ]);
    }
}
