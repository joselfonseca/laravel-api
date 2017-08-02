<?php

namespace App\Http\Controllers;

/**
 * Class ApiDocsController.
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
