<?php

namespace App\Http\Controllers\Api\Users;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Contracts\UsersServiceContract;

/**
 * Class ProfileController.
 */
class ProfileController extends Controller
{
    use Helpers;

    /**
     * @var \App\Contracts\UsersServiceContract
     */
    protected $service;

    /**
     * ProfileController constructor.
     *
     * @param \App\Contracts\UsersServiceContract $service
     */
    public function __construct(UsersServiceContract $service)
    {
        $this->service = $service;
    }

    /**
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        return $this->response->array($this->service->transform($this->service->find(Auth::user()->id)));
    }

    /**
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function update(Request $request)
    {
        $partial = false;
        if ($request->method() == 'PATCH') {
            $partial = true;
        }
        $user = $this->service->update(Auth::user()->id, $request->except('password'), $partial);
        return $this->response->array($this->service->transform($user));
    }

    /**
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $user = $this->service->updatePassword(Auth::user()->id, $request->all());
        return $this->response->array($this->service->transform($user));
    }
}
