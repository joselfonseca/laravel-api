<?php

namespace App\Services;

use Image;
use Illuminate\Support\Facades\Storage;
use App\Contracts\AssetsServiceContract;
use App\Contracts\ImageRenderServiceContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class ImageRenderService.
 */
class ImageRenderService implements ImageRenderServiceContract
{
    /**
     * @var \App\Contracts\AssetsServiceContract
     */
    protected $assetService;

    /**
     * ImageRenderService constructor.
     *
     * @param \App\Contracts\AssetsServiceContract $assetService
     */
    public function __construct(AssetsServiceContract $assetService)
    {
        $this->assetService = $assetService;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function render($id)
    {
        try {
            $file = $this->assetService->find($id);

            return $this->renderImage($file);
        } catch (ModelNotFoundException $e) {
            return $this->renderPlaceholder();
        }
    }

    /**
     * @param $path
     * @return mixed
     */
    protected function renderImage($file)
    {
        $image = Image::cache(function ($image) use ($file) {
            $image->make(Storage::get($file->path));
        }, 10, true);

        return $image->response();
    }

    /**
     * @return mixed
     */
    protected function renderPlaceholder()
    {
        $img = Image::canvas(800, 600, '#ff0000');

        return $img->response('jpg');
    }
}
