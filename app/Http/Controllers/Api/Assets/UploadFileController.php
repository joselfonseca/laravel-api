<?php

namespace App\Http\Controllers\Api\Assets;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use App\Contracts\AssetsServiceContract;

/**
 * Class UploadFileController.
 */
class UploadFileController extends Controller
{
    use Helpers;

    protected $service;

    /**
     * @var \App\Entities\Asset
     */
    protected $model;

    public function __construct(AssetsServiceContract $service)
    {
        $this->service = $service;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return $this
     */
    public function store(Request $request)
    {
        if ($request->isJson()) {
            $asset = $this->service->uploadFromUrl([
                'url' => $request->get('url'),
                'user' => $request->user(),
            ]);
        } else {
            $asset = $this->service->uploadFromDirectFile([
                'mime' => $request->header('Content-Type'),
                'content' => $request->getContent(),
                'Content-Length' => $request->header('Content-Length'),
                'user' => $request->user(),
            ]);
        }

        return $this->response->created(url('api/assets/'.$asset->uuid), $this->service->transform($asset));
    }
}
