<?php

namespace App\Http\Controllers\Api\Users;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Transformers\Users\UserTransformer;

/**
 * Class ProfileController
 * @package App\Http\Controllers\Api\Users
 */
class ProfileController extends Controller
{

    use Helpers;

    /**
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        return $this->response->item(Auth::user(), new UserTransformer());
    }

    /**
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ];
        if($request->method() == "PATCH") {
            $rules = [
                'name' => 'sometimes|required',
                'email' => 'sometimes|required|email|unique:users,email,'.$user->id,
            ];
        }
        $this->validate($request, $rules);
        // Except password as we don't want to let the users change a password from this endpoint
        $user->update($request->except('_token', 'password'));
        return $this->response->item($user->fresh(), new UserTransformer());
    }

}
