<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Transformers\Users\PermissionTransformer;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    protected $model;

    public function __construct(Permission $model)
    {
        $this->model = $model;
        $this->middleware('permission:List permissions')->only('index');
    }

    public function index(Request $request)
    {
        $paginator = $this->model->paginate($request->get('limit', config('app.pagination_limit')));
        if ($request->has('limit')) {
            $paginator->appends('limit', $request->get('limit'));
        }

        return fractal($paginator, new PermissionTransformer())->respond();
    }
}
