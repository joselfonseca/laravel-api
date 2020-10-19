<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Transformers\Users\UserTransformer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;

/**
 * Class UsersController.
 *
 * @author Jose Fonseca <jose@ditecnologia.com>
 */
class UsersController extends Controller
{
    use Helpers;

    /**
     * @var \App\Model\\App\Models\User
     */
    protected $model;

    /**
     * UsersController constructor.
     *
     * @param \App\Model\User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
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
        $paginator = $this->model->with('roles.permissions')->paginate($request->get('limit', config('app.pagination_limit', 20)));
        if ($request->has('limit')) {
            $paginator->appends('limit', $request->get('limit'));
        }

        return $this->response->paginator($paginator, new UserTransformer());
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $user = $this->model->with('roles.permissions')->byUuid($id)->firstOrFail();

        return $this->response->item($user, new UserTransformer());
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);
        $user = $this->model->create($request->all());
        if ($request->has('roles')) {
            $user->syncRoles($request['roles']);
        }

        return $this->response->created(url('api/users/'.$user->uuid));
    }

    /**
     * @param Request $request
     * @param $uuid
     * @return mixed
     */
    public function update(Request $request, $uuid)
    {
        $user = $this->model->byUuid($uuid)->firstOrFail();
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ];
        if ($request->method() == 'PATCH') {
            $rules = [
                'name' => 'sometimes|required',
                'email' => 'sometimes|required|email|unique:users,email,'.$user->id,
            ];
        }
        $this->validate($request, $rules);
        // Except password as we don't want to let the users change a password from this endpoint
        $user->update($request->except('_token', 'password'));
        if ($request->has('roles')) {
            $user->syncRoles($request['roles']);
        }

        return $this->response->item($user->fresh(), new UserTransformer());
    }

    /**
     * @param Request $request
     * @param $uuid
     * @return mixed
     */
    public function destroy(Request $request, $uuid)
    {
        $user = $this->model->byUuid($uuid)->firstOrFail();
        $user->delete();

        return $this->response->noContent();
    }
}
