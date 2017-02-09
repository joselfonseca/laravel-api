<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contracts\Users\UsersServiceContract;

/**
 * Class UsersController
 * @package App\Http\Controllers\Users
 */
class UsersController extends Controller
{

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
        $this->middleware('permission:Create users')->only('store');
        $this->middleware('permission:Update users')->only('update');
        $this->middleware('permission:Delete users')->only('destroy');
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $users = $this->service->get();
        return $this->service->transform($users);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        $attributes = $request->all();
        $this->service->create($attributes);
        return response()->json()->setStatusCode(201);
    }

    /**
     * @param Request $request
     * @param $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $uuid)
    {
        $user = $this->service->update($uuid, $request->all());
        return response()->json($this->service->transform($user));
    }

    /**
     * @param Request $request
     * @param $uuid
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Request $request, $uuid)
    {
        $this->service->delete($uuid);
        return response()->json()->setStatusCode(204);
    }
}
