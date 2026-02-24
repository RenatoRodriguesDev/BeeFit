<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Routine;

class RoutinesController extends Controller
{
    public function index()
    {
        $routines = Routine::where('user_id', auth()->id())
            ->withCount('exercises')
            ->get();

        return view('routines.index', compact('routines'));
    }
}