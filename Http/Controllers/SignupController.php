<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SignupController extends Controller
{
    public function show()
    {
        return view('auth.signup');
    }

    public function register(Request $request)
    {
        // OAuth-only signups: preserve optional intent and forward to the OAuth redirect.
        $intent = $request->input('intent');
        if ($intent) {
            session(['signup_intent' => $intent]);
        }

        return redirect()->route('oauth.redirect');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SignupController extends Controller
{
    public function show()
    {
        return view('auth.signup');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        $intent = session('signup_intent');
        session()->forget('signup_intent');

        if ($intent === 'entertainer') {
            return redirect('/entertainer/signup');
        }
        if ($intent === 'customer') {
            return redirect('/customer/signup');
        }

        return redirect('/');
    }
}
