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
            $answers = UserAnswer::with(['user', 'question', 'religion', 'timeSlot'])
                ->orderByDesc('id')
                ->paginate(10);

            return response()->json($answers);
        }

        return view('admin.answers.index');
    }
}
