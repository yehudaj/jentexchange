<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

// Auth routes (Breeze provided routes will also be available)
use App\Http\Controllers\Auth\GoogleController;

Route::get('/oauth/redirect', [GoogleController::class, 'redirect'])->name('oauth.redirect');
Route::get('/oauth/callback', [GoogleController::class, 'callback'])->name('oauth.callback');

// Hello world and signup
use App\Http\Controllers\SignupController;
Route::get('/hello', function () { return view('hello'); });
Route::get('/signup', [SignupController::class, 'show']);
Route::post('/signup', [SignupController::class, 'register']);

// Temporary debug endpoint for upload testing (no auth)
use Illuminate\Http\Request;
Route::post('/debug/upload-test', function(Request $request){
    $info = [
        'content_length' => $request->server('CONTENT_LENGTH'),
        'has_profile' => $request->hasFile('profile_image'),
        'profile_size' => $request->hasFile('profile_image') ? $request->file('profile_image')->getSize() : null,
        'has_background' => $request->hasFile('background_image'),
        'background_size' => $request->hasFile('background_image') ? $request->file('background_image')->getSize() : null,
        'headers' => collect(getallheaders())->toArray(),
    ];
    \Log::info('Debug upload endpoint hit', $info);
    return response()->json($info);
})->middleware('api');



// Simple auth helpers for layout links
use Illuminate\Support\Facades\Auth;

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Convenience routes for the current user
Route::get('/me/entertainer', function () {
    if (!auth()->check()) {
        return redirect('/login');
    }
    $user = auth()->user();
    if ($user->entertainer()->exists()) {
        return redirect()->route('entertainer.edit', ['entertainer' => $user->entertainer()->first()->id]);
    }
    return redirect('/entertainer/signup');
});

Route::get('/me/customer', function () {
    if (!auth()->check()) {
        return redirect('/login');
    }
    $user = auth()->user();
    if ($user->customer()->exists()) {
        return redirect()->route('customer.edit', ['customer' => $user->customer()->first()->id]);
    }
    return redirect('/customer/signup');
});

// Entertainer signup and edit (require OAuth login)
use App\Http\Controllers\EntertainerController;
// Normal auth-protected entertainer routes
Route::middleware('auth')->group(function () {
    Route::get('/entertainer/signup', [EntertainerController::class, 'create']);
    Route::post('/entertainer/signup', [EntertainerController::class, 'store']);
    Route::get('/entertainer/{entertainer}/edit', [EntertainerController::class, 'edit'])->name('entertainer.edit');
    Route::put('/entertainer/{entertainer}', [EntertainerController::class, 'update'])->name('entertainer.update');
});

// Temporary diagnostic route: call the controller update without auth/CSRF
Route::post('/debug/controller-upload/{entertainer}', [EntertainerController::class, 'update'])->withoutMiddleware(['auth', \App\Http\Middleware\VerifyCsrfToken::class]);

// Simple admin area — requires auth and admin status
use App\Http\Controllers\AdminController;
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users');

    Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.delete');
    // convenience admin root
    Route::get('/admin', function () { return redirect('/admin/users'); })->name('admin');
});

// Customer signup and edit (require OAuth login)
use App\Http\Controllers\CustomerController;
Route::middleware('auth')->group(function () {
    Route::get('/customer/signup', [CustomerController::class, 'create']);
    Route::post('/customer/signup', [CustomerController::class, 'store']);
    Route::get('/customer/{customer}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::put('/customer/{customer}', [CustomerController::class, 'update'])->name('customer.update');
});

