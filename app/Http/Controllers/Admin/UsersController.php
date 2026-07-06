<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->expectsJson()) {
            $users = User::orderByDesc('id')->paginate(10);

            return response()->json($users);
        }

        return view('admin.users.index');
    }
}
