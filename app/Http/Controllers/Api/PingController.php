<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;

/**
 * Class PingController
 * @package App\Http\Controllers\Api
 */
class PingController extends Controller
{

    use Helpers;
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->response->array([
            'status' => 'ok',
            'timestamp' => \Carbon\Carbon::now()
        ]);
    }
}
