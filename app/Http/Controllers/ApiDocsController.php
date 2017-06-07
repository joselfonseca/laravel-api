<?php

namespace App\Http\Controllers;

/**
 * Class ApiDocsController
 * @package App\Http\Controllers
 */
class ApiDocsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('apidocs');
    }
}
