<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleRedirectionController extends Controller
{
    public function redirect()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
