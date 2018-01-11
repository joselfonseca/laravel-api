<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Entities\Asset;
use App\Events\AssetWasCreated;
use App\Transformers\AssetTransformer;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;
use App\Contracts\AssetsServiceContract;
use App\Exceptions\BodyTooLargeException;
use Dingo\Api\Exception\ResourceException;
use GuzzleHttp\Exception\TransferException;
use League\Fractal\Serializer\DataArraySerializer;
use Dingo\Api\Exception\StoreResourceFailedException;
use Joselfonseca\LaravelApiTools\Contracts\FractalAble;
use Joselfonseca\LaravelApiTools\Traits\FilterableTrait;
use Joselfonseca\LaravelApiTools\Contracts\ValidateAble;
use Joselfonseca\LaravelApiTools\Traits\FractalAbleTrait;
use Joselfonseca\LaravelApiTools\Traits\ValidateAbleTrait;
use Joselfonseca\LaravelApiTools\Traits\OrderQueryResultHelper;
use Joselfonseca\LaravelApiTools\Traits\ProcessMultipleParameterHelper;

/**
 * Class AssetsService
 * @package App\Services
 */
class AssetsService implements FractalAble, ValidateAble, AssetsServiceContract
{

    use FractalAbleTrait,
        ValidateAbleTrait,
        FilterableTrait,
        OrderQueryResultHelper,
        ProcessMultipleParameterHelper;

    /**
     * @var array
     */
    protected $validMimes = [
        'image/jpeg' => [
            'type' => 'image',
            'extension' => 'jpeg',
        ],
        'image/jpg' => [
            'type' => 'image',
            'extension' => 'jpg',
        ],
        'image/png' => [
            'type' => 'image',
            'extension' => 'png',
        ],
    ];

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

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
    protected $resourceKey = "assets";

    /**
     * @var Asset
     */
    protected $model;

    /**
     * @var array
     */
    protected $includes = [];

    /**
     * AssetsService constructor.
     * @param Asset $model
     */
    public function __construct(Asset $model, Client $client)
    {
        $this->model = $model;
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function setTransformer() : TransformerAbstract
    {
        return app(AssetTransformer::class);
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
     * @return Asset
     */
    public function find($id)
    {
        return is_int($id) ? $this->model->findOrFail($id) : $this->model->byUuid($id)->firstOrFail();
    }

    /**
     * @param array $attributes
     * @return Asset
     * @throws ResourceException
     */
    public function create(array $attributes = [])
    {
        $this->runValidator($attributes, $this->validationCreateRules, $this->validationMessages);
        $model = $this->model->create($attributes);
        event(new AssetWasCreated($model));
        return $model;
    }

    /**
     * @param int|string $id
     * @param array $attributes
     * @return Asset
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
     * @param array $attributes
     * @return mixed
     */
    public function uploadFromDirectFile($attributes = [])
    {
        $attributes['content'] = ! (base64_decode($attributes['content'])) ? $attributes['content'] : base64_decode($attributes['content']);
        $this->runValidator($attributes, [
            'mime' => 'required',
            'Content-Length' => 'required',
            'content' => 'required'
        ], $this->validationMessages);
        $this->validateMime($attributes['mime']);
        $attributes['type'] = $this->validMimes[$attributes['mime']]['type'];
        $this->validateBodySize($attributes['Content-Length'], $attributes['content']);
        $attributes['path'] = $this->storeInFileSystem($attributes);
        $attributes['user_id'] = ! empty($attributes['user']) ? $attributes['user']->id : null;

        return $this->create($attributes);
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function uploadFromUrl($attributes = [])
    {
        $this->runValidator($attributes, [
            'url' => 'required',
            'user' => 'required'
        ], $this->validationMessages);
        $response = $this->callFileUrl($attributes['url']);
        $attributes['mime'] = $response->getHeader('content-type')[0];
        $this->validateMime($attributes['mime']);
        $attributes['type'] = $this->validMimes[$attributes['mime']]['type'];
        $attributes['content'] = $response->getBody();
        $attributes['path'] = $this->storeInFileSystem($attributes);
        $attributes['user_id'] = ! empty($attributes['user']) ? $attributes['user']->id : null;

        return $this->create($attributes);
    }

    /**
     * @param array $attributes
     * @return string
     */
    protected function storeInFileSystem(array $attributes)
    {
        $path = md5(str_random(16).date('U')).'.'.$this->validMimes[$attributes['mime']]['extension'];
        Storage::put($path, $attributes['content']);

        return $path;
    }

    /**
     * @param $url
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function callFileUrl($url)
    {
        try {
            $response = $this->client->get($url);
            if ($response->getStatusCode() != 200) {
                throw new StoreResourceFailedException('Validation Error', [
                    'url' => 'The url seems unreachable',
                ]);
            }

            return $response;
        } catch (TransferException $e) {
            throw new StoreResourceFailedException('Validation Error', [
                'url' => 'The url seems to be unreachable: '.$e->getCode(),
            ]);
        }
    }

    /**
     * @param $mime
     */
    protected function validateMime($mime)
    {
        if (! array_key_exists($mime, $this->validMimes)) {
            throw new StoreResourceFailedException('Validation Error', [
                'Content-Type' => 'The Content Type sent is not valid',
            ]);
        }
    }

    /**
     * @param $contentLength
     * @param $content
     * @throws \App\Exceptions\BodyTooLargeException
     */
    protected function validateBodySize($contentLength, $content)
    {
        if ($contentLength > config('files.maxsize', 1000000)) {
            throw new BodyTooLargeException();
        }
        if (mb_strlen($content) > config('files.maxsize', 1000000)) {
            throw new BodyTooLargeException();
        }
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
