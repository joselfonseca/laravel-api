<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use App\Contracts\Users\UsersServiceContract;

/**
 * Class UsersController
 * @package App\Http\Controllers\Users
 */
class UsersController extends Controller
{

    use Helpers;

    /**
     * @var UsersServiceContract
     */
    protected $service;

    /**
     * UsersController constructor.
     * @param UsersServiceContract $service
     */
    public function __construct(UsersServiceContract $service)
    {
        $this->service = $service;
        $this->middleware('permission:List users')->only('index');
        $this->middleware('permission:List users')->only('show');
        $this->middleware('permission:Create users')->only('store');
        $this->middleware('permission:Update users')->only('update');
        $this->middleware('permission:Delete users')->only('destroy');
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $attributes = $request->except('page');
        $limit = isset($attributes['limit']) ? $attributes['limit'] : env('PAGINATION_VALUE', 20);
        $users = $this->service->get($attributes, $limit);
        $users->appends($attributes);
        return $this->response->array($this->service->transform($users));
    }


    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->response->array($this->service->transform($this->service->find($id)));
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $attributes = $request->all();
        $user = $this->service->create($attributes);
        return $this->response->created(url('api/users/'.$user->uuid), $this->service->transform($user));
    }


    /**
     * @param Request $request
     * @param $uuid
     * @return mixed
     */
    public function update(Request $request, $uuid)
    {
        $user = $this->service->update($uuid, $request->all());
        return $this->response->array($this->service->transform($user));
    }


    /**
     * @param Request $request
     * @param $uuids
     * @return mixed
     */
    public function destroy(Request $request, $uuids)
    {
        $this->service->delete($uuids);
        return $this->response->noContent();
    }
}
