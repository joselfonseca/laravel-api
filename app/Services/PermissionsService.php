<?php

namespace App\Services;

use App\Entities\Permission;
use App\Contracts\PermissionsServiceContract;
use App\Transformers\PermissionTransformer;
use League\Fractal\TransformerAbstract;
use Dingo\Api\Exception\ResourceException;
use League\Fractal\Serializer\DataArraySerializer;
use Joselfonseca\LaravelApiTools\Contracts\FractalAble;
use Joselfonseca\LaravelApiTools\Traits\FilterableTrait;
use Joselfonseca\LaravelApiTools\Contracts\ValidateAble;
use Joselfonseca\LaravelApiTools\Traits\FractalAbleTrait;
use Joselfonseca\LaravelApiTools\Traits\ValidateAbleTrait;
use Joselfonseca\LaravelApiTools\Traits\OrderQueryResultHelper;
use Joselfonseca\LaravelApiTools\Traits\ProcessMultipleParameterHelper;

/**
 * Class PermissionsService
 * @package App\Services
 */
class PermissionsService implements FractalAble, ValidateAble, PermissionsServiceContract
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

    ];

    /**
     * @var array
     */
    protected $validationUpdateRules = [

    ];

    /**
     * @var array
     */
    protected $validationMessages = [

    ];

    /**
     * @var string
     */
    protected $resourceKey = "permissions";

    /**
     * @var Permission
     */
    protected $model;

    /**
     * @var array
     */
    protected $includes = [];

    /**
     * PermissionsService constructor.
     * @param Permission $model
     */
    public function __construct(Permission $model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function setTransformer() : TransformerAbstract
    {
        return app(PermissionTransformer::class);
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
     * @return Permission
     */
    public function find($id)
    {
        return is_int($id) ? $this->model->findOrFail($id) : $this->model->byUuid($id)->firstOrFail();
    }

    /**
     * @param array $attributes
     * @return Permission
     * @throws ResourceException
     */
    public function create(array $attributes = [])
    {
        $this->runValidator($attributes, $this->validationCreateRules, $this->validationMessages);
        $model = $this->model->create($attributes);
        return $model;
    }

    /**
     * @param int|string $id
     * @param array $attributes
     * @return Permission
     * @throws ResourceException
     */
    public function update($id, array $attributes = [])
    {
        $model = $this->find($id);
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

    /**
     * @return array
     */
    public function getFilterableFields()
    {
        return [

        ];
    }
}
