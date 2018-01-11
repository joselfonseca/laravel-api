<?php

namespace App\Http\Controllers\Api\Users;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use App\Contracts\PermissionsServiceContract;

/**
 * Class PermissionsController.
 */
class PermissionsController extends Controller
{
    use Helpers;

    /**
     * @var
     */
    protected $service;

    /**
     * PermissionsController constructor.
     *
     * @param \App\Contracts\PermissionsServiceContract $service
     */
    public function __construct(PermissionsServiceContract $service)
    {
        $this->service = $service;
        $this->middleware('permission:List permissions')->only('index');
    }

    /**
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function index(Request $request)
    {
        return $this->response->array($this->service->transform($this->service->get($request->all(), $request->get('limit', config('app.pagination_limit')))));
    }
}
