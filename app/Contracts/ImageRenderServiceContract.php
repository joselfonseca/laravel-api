<?php

namespace App\Contracts;

/**
 * Class ImageRenderService
 *
 * @package App\Services
 */
interface ImageRenderServiceContract
{
    /**
     * @param $id
     * @return mixed
     */
    public function render($id);
}