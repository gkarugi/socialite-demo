<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OauthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function redirectToProvider()
    {
        // To add functionality to redirect to oauth provider
    }

    public function handleProviderCallback()
    {
        // To add functionality to handle callback from the oauth provider
    }
}
