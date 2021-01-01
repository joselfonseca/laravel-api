<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Transformers\Users\RoleTransformer;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    protected $model;

    public function __construct(Role $model)
    {
        $this->model = $model;
        $this->middleware('permission:List roles')->only('index');
        $this->middleware('permission:List roles')->only('show');
        $this->middleware('permission:Create roles')->only('store');
        $this->middleware('permission:Update roles')->only('update');
        $this->middleware('permission:Delete roles')->only('destroy');
    }

    public function index(Request $request)
    {
        $paginator = $this->model->with('permissions')->paginate($request->get('limit', config('app.pagination_limit')));
        if ($request->has('limit')) {
            $paginator->appends('limit', $request->get('limit'));
        }

        return fractal($paginator, new RoleTransformer())->respond();
    }

    public function show($id)
    {
        $role = $this->model->with('permissions')->byUuid($id)->firstOrFail();

        return fractal($role, new RoleTransformer())->respond();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $role = $this->model->create($request->all());
        if ($request->has('permissions')) {
            $role->syncPermissions($request['permissions']);
        }

        return fractal($role, new RoleTransformer())->respond(201);
    }

    public function update(Request $request, $uuid)
    {
        $role = $this->model->byUuid($uuid)->firstOrFail();
        $this->validate($request, [
            'name' => 'required',
        ]);
        $role->update($request->except('_token'));
        if ($request->has('permissions')) {
            $role->syncPermissions($request['permissions']);
        }

        return fractal($role->fresh(), new RoleTransformer())->respond();
    }

    public function destroy(Request $request, $uuid)
    {
        $role = $this->model->byUuid($uuid)->firstOrFail();
        $role->delete();

        return response()->json(null, 204);
    }
}
