<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        if (! auth()->user() || ! auth()->user()->isAdmin()) {
            abort(403);
        }
        $users = User::with('roles')->orderBy('id', 'desc')->get();
        return view('admin.users', ['users' => $users]);
    }

    public function destroy(User $user)
    {
        if (! auth()->user() || ! auth()->user()->isAdmin()) {
            abort(403);
        }
        // Prevent deleting yourself accidentally
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Cannot delete yourself');
        }
        $user->delete();
        return redirect()->back()->with('status', 'User deleted');
    }
}
