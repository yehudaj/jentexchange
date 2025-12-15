<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        // Preserve an optional intent (e.g. ?intent=entertainer) so callback can route appropriately
        $intent = request()->query('intent');
        if ($intent) {
            session(['signup_intent' => $intent]);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Create or find the user by email. If created, default to CUSTOMER role.
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            ['name' => $googleUser->getName() ?: $googleUser->getNickname(), 'password' => bcrypt(bin2hex(random_bytes(16)))]
        );

        // If created just now, ensure customer role exists
        if (! $user->hasRole(User::ROLE_CUSTOMER)) {
            $user->addRole(User::ROLE_CUSTOMER);
        }

        // Save some oauth profile info in session to prefill any signup forms
        session(['oauth_profile' => [
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'avatar' => $googleUser->getAvatar(),
        ]]);

        Auth::login($user, true);

        // If signup intent was set to entertainer, redirect to entertainer signup/edit flow
        $intent = session('signup_intent');
        session()->forget('signup_intent');

        if ($intent === 'entertainer') {
            // If the user already has an entertainer profile, send to edit; otherwise to create
            if ($user->entertainer()->exists()) {
                return redirect()->route('entertainer.edit', ['entertainer' => $user->entertainer()->first()->id]);
            }

            return redirect('/entertainer/signup');
        }

        // default: go to home
        $appUrl = config('app.url') ?: url('/');
        return redirect()->to($appUrl);
    }
}
