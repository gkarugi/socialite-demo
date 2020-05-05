<?php

namespace App\Http\Controllers;

use App\SocialProfile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class OauthController extends Controller
{
    public $scopes = [
        'facebook' => [
            'manage_pages', 'publish_pages','pages_messaging'
        ],

        'twitter' => [],
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function redirectToProvider(Request $request, $provider)
    {
        $scopes = $this->scopes[$provider];

        session()->put('action_type',$request->get('action_type'));

        // as far as I know, twitter doesn't seem to use scopes
        if (!$provider == 'twitter') {
            return Socialite::driver($provider)
                ->scopes($scopes)
                ->redirect();
        } else {
            return Socialite::driver($provider)
                ->redirect();
        }
    }

    public function handleProviderCallback(Request $request, $provider)
    {
        if (session()->pull('action_type') == 'login') {
            return $this->login($request,$provider);
        } else {
            return $this->register($request,$provider);
        }
    }

    protected function login(Request $request, $provider)
    {
        $socialUser = Socialite::driver($provider)->user();

        $socialProfile = SocialProfile::where('provider', $provider)->where('provider_user_id',$socialUser->getEmail())->first();

        if ($socialProfile) {
            Auth::login($socialProfile->sociable);
            return redirect()->intended(route('home'));
        } else {
            return redirect()->route('register')
                ->withError('No active account was found with your social media credentials. Please register to continue.');
        }
    }

    protected function register(Request $request, $provider)
    {
        $socialUser = Socialite::driver($provider)->user();

        $userExists = User::where('email', $socialUser->getEmail())->first();
        $socialProfile = SocialProfile::where('provider', $provider)->where('provider_user_id', $socialUser->getEmail())->first();

        if ($userExists && $socialProfile) {
            Auth::login($userExists);

            return redirect()->route('home')
                ->withStatus('An account already exists with the social account given. We have automatically logged you into your account');

        } elseif ($userExists && !$socialProfile) {
            $userExists->socialProfiles()->updateOrCreate([
                'provider' => $provider,
                'provider_user_id' => $socialUser->email,
                'oauth_token' => $socialUser->token
            ]);

            Auth::login($userExists);

            return redirect()
                ->route('home')
                ->withStatus('An account already exists but we have linked it to your social media account');

        } elseif (!$userExists && !$socialProfile) {
            DB::beginTransaction();

            try {
                $newUser = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random()),
                    'set_new_password' => true,
                ]);

                $newUser->socialProfiles()->updateOrCreate([
                    'provider' => $provider,
                    'provider_user_id' => $socialUser->email,
                    'oauth_token' => $socialUser->token
                ]);
            } catch (\Exception $exception) {
               DB::rollBack();

               return redirect()->route('login')->withError('An error occurred while processing your request');
            }

            DB::commit();

            Auth::login($newUser);

            return redirect()
                ->route('home')
                ->withStatus('Your account has been setup successfully.');
        }
    }
}
