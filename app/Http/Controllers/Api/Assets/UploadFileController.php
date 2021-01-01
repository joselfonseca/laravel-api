<?php

namespace App\Http\Controllers\Api\Assets;

use App\Events\AssetWasCreated;
use App\Exceptions\BodyTooLargeException;
use App\Exceptions\StoreResourceFailedException;
use App\Http\Controllers\Controller;
use App\Transformers\Assets\AssetTransformer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadFileController extends Controller
{
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

    protected $client;

    protected $model;

    public function __construct(Client $client, \App\Models\Asset $model)
    {
        $this->client = $client;
        $this->model = $model;
    }

    public function store(Request $request)
    {
        if ($request->isJson()) {
            $asset = $this->uploadFromUrl([
                'url' => $request->get('url'),
                'user' => $request->user(),
            ]);
        } elseif ($request->hasFile('file')) {
            $file = $request->file('file')->getRealPath();
            $asset = $this->uploadFromDirectFile([
                'mime' => $request->file('file')->getClientMimeType(),
                'content' => file_get_contents($file),
                'Content-Length' => $request->header('Content-Length'),
                'user' => $request->user(),
            ]);
        } else {
            $body = ! (base64_decode($request->getContent())) ? $request->getContent() : base64_decode($request->getContent());
            $asset = $this->uploadFromDirectFile([
                'mime' => $request->header('Content-Type'),
                'content' => $body,
                'Content-Length' => $request->header('Content-Length'),
                'user' => $request->user(),
            ]);
        }

        event(new AssetWasCreated($asset));

        return fractal($asset, new AssetTransformer())->respond(201);
    }

    protected function uploadFromDirectFile($attributes = [])
    {
        $this->validateMime($attributes['mime']);
        $this->validateBodySize($attributes['Content-Length'], $attributes['content']);
        $path = $this->storeInFileSystem($attributes);

        return $this->storeInDatabase($attributes, $path);
    }

    protected function uploadFromUrl($attributes = [])
    {
        $response = $this->callFileUrl($attributes['url']);
        $attributes['mime'] = $response->getHeader('content-type')[0];
        $this->validateMime($attributes['mime']);
        $attributes['content'] = $response->getBody();
        $path = $this->storeInFileSystem($attributes);

        return $this->storeInDatabase($attributes, $path);
    }

    protected function storeInDatabase(array $attributes, $path)
    {
        $file = $this->model->create([
            'type' => $this->validMimes[$attributes['mime']]['type'],
            'path' => $path,
            'mime' => $attributes['mime'],
            'user_id' => ! empty($attributes['user']) ? $attributes['user']->id : null,
        ]);

        return $file;
    }

    protected function storeInFileSystem(array $attributes)
    {
        $path = md5(Str::random(16).date('U')).'.'.$this->validMimes[$attributes['mime']]['extension'];
        Storage::put($path, $attributes['content']);

        return $path;
    }

    protected function callFileUrl($url)
    {
        try {
            return $this->client->get($url);
        } catch (TransferException $e) {
            throw new StoreResourceFailedException('Validation Error', [
                'url' => 'The url seems to be unreachable: '.$e->getCode(),
            ]);
        }
    }

    protected function validateMime($mime)
    {
        if (! array_key_exists($mime, $this->validMimes)) {
            throw new StoreResourceFailedException('Validation Error', [
                'Content-Type' => 'The Content Type sent is not valid',
            ]);
        }
    }

    protected function validateBodySize($contentLength, $content)
    {
        if ($contentLength > config('files.maxsize', 1000000) || mb_strlen($content) > config('files.maxsize', 1000000)) {
            throw new BodyTooLargeException();
        }
    }
}
