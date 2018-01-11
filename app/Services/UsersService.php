<?php

namespace App\Services;

use App\Entities\User;
use App\Transformers\UserTransformer;
use App\Contracts\UsersServiceContract;
use League\Fractal\TransformerAbstract;
use Illuminate\Contracts\Hashing\Hasher;
use Dingo\Api\Exception\ResourceException;
use League\Fractal\Serializer\DataArraySerializer;
use Joselfonseca\LaravelApiTools\Contracts\FractalAble;
use Joselfonseca\LaravelApiTools\Contracts\ValidateAble;
use Joselfonseca\LaravelApiTools\Traits\FilterableTrait;
use Joselfonseca\LaravelApiTools\Traits\FractalAbleTrait;
use Joselfonseca\LaravelApiTools\Traits\ValidateAbleTrait;
use Joselfonseca\LaravelApiTools\Traits\OrderQueryResultHelper;
use Joselfonseca\LaravelApiTools\Traits\ProcessMultipleParameterHelper;

/**
 * Class UsersService
 * @package App\Services
 */
class UsersService implements FractalAble, ValidateAble, UsersServiceContract
{

    use FractalAbleTrait,
        ValidateAbleTrait,
        FilterableTrait,
        OrderQueryResultHelper,
        ProcessMultipleParameterHelper;

    /**
     * @var array
     */
    protected $validationCreateRules = [
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
    ];

    /**
     * @var array
     */
    protected $validationUpdateRules = [
        'name' => 'required'
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
     * @var array
     */
    protected $includes = ['roles.permissions'];

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
     * @return string
     */
    public function setSerializer()
    {
        return DataArraySerializer::class;
    }

    /**
     * @param array $attributes
     * @param int $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get(array $attributes = [], $limit = 20)
    {
        $model = $this->model->with($this->includes);
        $this->applyFilters($model, $attributes);
        $this->processOrderingRules($attributes);
        $this->applyOrderingRules($model);
        if (!empty($limit)) {
            $paginator = $model->paginate($limit);
            $paginator->appends('limit', $limit);
            return $paginator;
        }
        return $model->get();
    }

    /**
     * @param int|string $id
     * @return Users
     */
    public function find($id)
    {
        return is_int($id) ? $this->model->findOrFail($id) : $this->model->byUuid($id)->firstOrFail();
    }

    /**
     * @param array $attributes
     * @return Users
     * @throws ResourceException
     */
    public function create(array $attributes = [])
    {
        $this->runValidator($attributes, $this->validationCreateRules, $this->validationMessages);
        $model = $this->model->create($attributes);
        $this->validateAndUpdateRoles($model, $attributes);
        return $model;
    }

    /**
     * @param int|string $id
     * @param array $attributes
     * @param bool $partial
     * @return Users
     * @throws ResourceException
     */
    public function update($id, array $attributes = [], $partial = false)
    {
        $model = $this->find($id);
        $this->validationUpdateRules['email'] = 'required|email|unique:users,email,'.$model->id;
        if ($partial) {
            $this->validationUpdateRules = collect($this->validationUpdateRules)->map(function ($value) {
                return 'sometimes|'.$value;
            })->toArray();
        }
        $this->runValidator($attributes, $this->validationUpdateRules, $this->validationMessages);
        $model->fill($attributes);
        $model->save();
        $this->validateAndUpdateRoles($model, $attributes);
        return $model->fresh();
    }

    /**
     * @param $model
     * @param $attributes
     */
    protected function validateAndUpdateRoles($model, $attributes)
    {
        if (array_key_exists('roles', $attributes)) {
            $model->syncRoles($attributes['roles']);
        }
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

    /**
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    public function updatePassword($id, array $attributes = [])
    {
        $user = $this->find($id);
        $this->runValidator($attributes, [
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ], $this->validationMessages);
        if (! app(Hasher::class)->check($attributes['current_password'], $user->password)) {
            throw new ResourceException('Validation Issue', [
                'old_password' => 'The current password is incorrect',
            ]);
        }
        $user->password = $attributes['password'];
        $user->save();
        return $user->fresh();
    }

    /**
     * @return array
     */
    public function getFilterableFields()
    {
        return [

        ];
    }
}
