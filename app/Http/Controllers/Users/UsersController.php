<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contracts\Users\UsersServiceContract;
use Joselfonseca\LaravelApiTools\Traits\ResponseBuilder;

/**
 * Class UsersController
 * @package App\Http\Controllers\Users
 */
class UsersController extends Controller
{

    use ResponseBuilder;

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
     * @return mixed
     */
    public function index()
    {
        $users = $this->service->get();
        return response()->json($this->service->transform($users));
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return response()->json($this->service->transform($this->service->find($id)));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        $attributes = $request->all();
        $user = $this->service->create($attributes);
        return $this->created(url('api/users/'.$user->uuid), $this->service->transform($user));
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
        return $this->noContent();
    }
}
