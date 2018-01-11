<?php

namespace App\Http\Controllers\Api\Users;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use App\Contracts\UsersServiceContract;

/**
 * Class UsersController.
 *
 * @author Jose Fonseca <jose@ditecnologia.com>
 */
class UsersController extends Controller
{
    use Helpers;

    /**
     * @var \App\Contracts\UsersServiceContract
     */
    protected $service;

    /**
     * UsersController constructor.
     *
     * @param \App\Contracts\UsersServiceContract $service
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
     * Returns the Users resource with the roles relation.
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $collection = $this->service->get($request->all(), $request->get('limit', config('app.pagination_limit')));

        return $this->response->array($this->service->transform($collection));
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
        $user = $this->service->create($request->all());

        return $this->response->created(url('api/users/'.$user->uuid));
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $partial = false;
        if ($request->method() == 'PATCH') {
            $partial = true;
        }
        $user = $this->service->update($id, $request->except('_token', 'password'), $partial);

        return $this->response->array($this->service->transform($user));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $this->service->delete($id);

        return $this->response->noContent();
    }
}
