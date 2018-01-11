<?php

namespace App\Http\Controllers\Api\Users;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use App\Contracts\RolesServiceContract;

/**
 * Class RolesController.
 */
class RolesController extends Controller
{
    use Helpers;

    /**
     * @var \App\Contracts\RolesServiceContract
     */
    protected $service;

    /**
     * RolesController constructor.
     *
     * @param \App\Contracts\RolesServiceContract $service
     */
    public function __construct(RolesServiceContract $service)
    {
        $this->service = $service;
        $this->middleware('permission:List roles')->only('index');
        $this->middleware('permission:List roles')->only('show');
        $this->middleware('permission:Create roles')->only('store');
        $this->middleware('permission:Update roles')->only('update');
        $this->middleware('permission:Delete roles')->only('destroy');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        return $this->response->array($this->service->transform($this->service->get($request->toArray(), $request->get('limit', config('app.pagination_limit')))));
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
        $model = $this->service->create($request->all());
        return $this->response->created(url('api/roles/'.$model->uuid));
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $model = $this->service->update($id, $request->all());
        return $this->response->array($this->service->transform($model));
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
