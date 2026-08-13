<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserAnswer;
use Illuminate\Http\Request;

class AnswersController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->expectsJson()) {
            $query = UserAnswer::with(['user', 'question', 'religion', 'timeSlot'])
                ->orderByDesc('id');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('religion', function ($r) use ($search) {
                        $r->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('question', function ($qp) use ($search) {
                        $qp->where('question', 'like', "%{$search}%");
                    });
                });
            }

            $perPage = (int) $request->input('per_page', 10);

            return response()->json($query->paginate($perPage));
        }

        return view('admin.answers.index');
    }
}
