<?php

namespace App\Services;

use App\Entities\Role;
use App\Transformers\RoleTransformer;
use League\Fractal\TransformerAbstract;
use App\Contracts\RolesServiceContract;
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
 * Class RolesService
 * @package App\Services
 */
class RolesService implements FractalAble, ValidateAble, RolesServiceContract
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
        'name' => 'required'
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
    protected $resourceKey = "roles";

    /**
     * @var Role
     */
    protected $model;

    /**
     * @var array
     */
    protected $includes = ['permissions'];

    /**
     * RolesService constructor.
     * @param Role $model
     */
    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function setTransformer() : TransformerAbstract
    {
        return app(RoleTransformer::class);
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
     * @return Role
     */
    public function find($id)
    {
        return is_int($id) ? $this->model->findOrFail($id) : $this->model->byUuid($id)->firstOrFail();
    }

    /**
     * @param array $attributes
     * @return Role
     * @throws ResourceException
     */
    public function create(array $attributes = [])
    {
        $this->runValidator($attributes, $this->validationCreateRules, $this->validationMessages);
        $model = $this->model->create($attributes);
        $this->validateAndSyncPermissions($model, $attributes);
        return $model;
    }

    /**
     * @param int|string $id
     * @param array $attributes
     * @return Role
     * @throws ResourceException
     */
    public function update($id, array $attributes = [])
    {
        $model = $this->find($id);
        $this->runValidator($attributes, $this->validationUpdateRules, $this->validationMessages);
        $model->fill($attributes);
        $model->save();
        $this->validateAndSyncPermissions($model, $attributes);
        return $model->fresh();
    }

    protected function validateAndSyncPermissions($model, $attributes)
    {
        if (array_key_exists('permissions', $attributes)) {
            $model->syncPermissions($attributes['permissions']);
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
	 * @return array
	 */
	public function getFilterableFields()
	{
		return [

		];
	}
}
