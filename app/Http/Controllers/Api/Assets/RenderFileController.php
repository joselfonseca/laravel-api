<?php

namespace App\Http\Controllers\Api\Assets;

use App\Http\Controllers\Controller;
use App\Contracts\ImageRenderServiceContract;

/**
 * Class RenderFileController.
 */
class RenderFileController extends Controller
{
    /**
     * @var \App\Contracts\ImageRenderServiceContract
     */
    protected $service;

    /**
     * RenderFileController constructor.
     *
     * @param \App\Contracts\ImageRenderServiceContract $service
     */
    public function __construct(ImageRenderServiceContract $service)
    {
        $this->service = $service;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->service->render($id);
    }
}
