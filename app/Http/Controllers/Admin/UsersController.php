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
            $query = User::orderByDesc('id');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('user_type', 'like', "%{$search}%")
                      ->orWhere('provider', 'like', "%{$search}%");
                });
            }

            $perPage = (int) $request->input('per_page', 10);

            $users = $query->paginate($perPage);

            return response()->json($users);
        }

        return view('admin.users.index');
    }
}
