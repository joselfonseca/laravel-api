<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;

/**
 * Class PingController.
 *
 * @author Jose Fonseca <jose@ditecnologia.com>
 */
class PingController extends Controller
{
    use Helpers;

    /**
     * Responds with a status for heath check.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->response->array([
            'status' => 'ok',
            'timestamp' => \Carbon\Carbon::now(),
        ]);
    }
}
