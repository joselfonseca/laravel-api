<?php

namespace App\Contracts;

/**
 * Class ImageRenderService.
 */
interface ImageRenderServiceContract
{
    /**
     * @param $id
     * @return mixed
     */
    public function render($id);
}
