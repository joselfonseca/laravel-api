<?php

namespace App\Services\Users;

use App\Entities\User;
use League\Fractal\TransformerAbstract;
use App\Transformers\Users\UserTransformer;
use App\Contracts\Users\UsersServiceContract;
use Joselfonseca\LaravelApiTools\Contracts\FractalAble;
use Joselfonseca\LaravelApiTools\Contracts\ValidateAble;
use Joselfonseca\LaravelApiTools\Traits\FractalAbleTrait;
use Joselfonseca\LaravelApiTools\Traits\ValidateAbleTrait;
use Joselfonseca\LaravelApiTools\Exceptions\ValidationException;

/**
 * Class UsersService
 * @package App\Services
 */
class UsersService implements FractalAble, ValidateAble, UsersServiceContract
{

    use FractalAbleTrait, ValidateAbleTrait;

    /**
     * @var array
     */
    protected $validationCreateRules = [
        'name' => 'required',
        'email' => 'required|unique:users,email',
        'password' => 'required|min:8|confirmed'
    ];

    /**
     * @var array
     */
    protected $validationUpdateRules = [
        'name' => 'sometimes|required',
        'email' => 'sometimes|required|unique:users,email'
    ];

    /**
     * @var array
     */
    protected $validationMessages = [

    ];

    /**
     * @var string
     */
    protected $resourceKey = "users";

    /**
     * @var User
     */
    protected $model;

    /**
     * Declare the includes to use in the with query
     * @var array
     */
    protected $includes = [];

    /**
     * UsersService constructor.
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function setTransformer() : TransformerAbstract
    {
        return app(UserTransformer::class);
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function get($limit = 20)
    {
        $model = $this->model->with($this->includes);
        if (!empty($limit)) {
            return $model->paginate($limit);
        }
        return $model->get();
    }

    /**
     * @param int|string $id
     * @return User
     */
    public function find($id)
    {
        return is_int($id) ? $this->model->findOrFail($id) : $this->model->byUuid($id)->firstOrFail();
    }

    /**
     * @param array $attributes
     * @return User
     * @throws ValidationException
     */
    public function create(array $attributes = [])
    {
        $this->runValidator($attributes, $this->validationCreateRules, $this->validationMessages);
        $attributes['password'] = bcrypt($attributes['password']);
        $model = $this->model->create($attributes);
        return $model;
    }

    /**
     * @param int|string $id
     * @param array $attributes
     * @return User
     * @throws ValidationException
     */
    public function update($id, array $attributes = [])
    {
        $model = $this->find($id);
        if (isset($attributes['email']) && $attributes['email'] != $model->email) {
            $this->validationUpdateRules['email'] = 'sometimes|required|unique:users,email,'.$model->id;
        }
        if (isset($attributes['password'])) {
            unset($attributes['password']);
        }
        $this->runValidator($attributes, $this->validationUpdateRules, $this->validationMessages);
        $model->fill($attributes);
        $model->save();
        return $model->fresh();
    }

    /**
     * @param int|string $id
     * @return bool
     */
    public function delete($id)
    {
        $model = $this->find($id);
        $model->delete();
        return true;
    }
}
