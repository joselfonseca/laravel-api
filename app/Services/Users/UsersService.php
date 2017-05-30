<?php

namespace App\Services\Users;

use App\Entities\User;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\TransformerAbstract;
use Dingo\Api\Exception\ResourceException;
use App\Transformers\Users\UserTransformer;
use App\Contracts\Users\UsersServiceContract;
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

    use FractalAbleTrait, ValidateAbleTrait, FilterableTrait, OrderQueryResultHelper, ProcessMultipleParameterHelper;

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
        'name' => 'required',
        'email' => 'required|unique:users,email'
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
    public function setTransformer(): TransformerAbstract
    {
        return app(UserTransformer::class);
    }

    /**
     * Sets the serializer to be used in the transformation
     * @return SerializerAbstract
     */
    public function setSerializer()
    {
        return new JsonApiSerializer(url('api'));
    }

    /**
     * @param array $attributes
     * @param int $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get(array $attributes = [], $limit = 20)
    {
        $model = $this->model->with($this->includes);
        $this->addFilter($attributes);
        $this->applyFilters($model, $attributes);

        $this->processOrderingRules($attributes);
        $this->applyOrderingRules($model);

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
     * @throws ResourceException
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
     * @param bool $partial
     * @return User
     * @throws ResourceException
     */
    public function update($id, array $attributes = [], $partial = false)
    {
        $model = $this->find($id);
        if (array_key_exists('email', $attributes)) {
            $this->validationUpdateRules['email'] = 'required|unique:users,email,' . $model->id;
        }
        if (array_key_exists('password', $attributes)) {
            $this->validationUpdateRules['password'] = 'required|min:8|confirmed';
        }
        if ($partial) {
            $this->validationUpdateRules = array_map(function ($value) {
                return "sometimes|" . $value;
            }, $this->validationUpdateRules);
        }
        $this->runValidator($attributes, $this->validationUpdateRules, $this->validationMessages);
        if (isset($attributes['password'])) {
            $attributes['password'] = bcrypt($attributes['password']);
        }
        $model->fill($attributes);
        $model->save();
        return $model->fresh();
    }

    /**
     * @param int|string $ids
     * @return bool
     */
    public function delete($ids)
    {
        $parameters = $this->processParameter($ids);

        foreach ($parameters as $id) {
            $model = $this->find($id);
            $model->delete();
        }

        return true;
    }

    /**
     * Filterable fields
     * @return array
     */
    public function getFilterableFields()
    {
        return [
            'name' => 'partial',
            'email' => 'partial',
        ];
    }
}
