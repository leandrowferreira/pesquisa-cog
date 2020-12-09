<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show()
    {
        return Auth::user()->only(['id', 'aviso_privacidade']);
    }

    public function update(Request $request, User $user)
    {
        if ($user->id != Auth::user()->id) {
            return null;
        }
        $user->update(['aviso_privacidade' => $request->privacidade]);
        return 1;
    }
}
