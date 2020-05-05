<?php

namespace App\Http\Controllers;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public $api;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $fb = app('Integrations\Facebook');

        $this->middleware(function ($request, $next) use ($fb) {
            $fb->setDefaultAccessToken(auth()->user()->facebookToken());
            $this->api = $fb;

            return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $socialProfiles = auth()->user()->socialProfiles;

        return view('home', compact('socialProfiles'));
    }

    public function listPages()
    {
        $facebookProfile = auth()->user()->socialProfiles()
            ->where('provider', '=', 'facebook')
            ->where('oauth_token', '!=', null)
            ->first();

        try {
            // Get the \Facebook\GraphNodes\GraphUser object for the current user.
            // If you provided a 'default_access_token', the '{access-token}' is optional.
            $response = $this->api->get('/me/accounts', auth()->user()->facebookToken());
        } catch(FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $pages = $response->getGraphEdge()->asArray();

        return view('fbPages', compact('pages'));
    }
}
