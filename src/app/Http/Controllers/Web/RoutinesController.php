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

    public function show(Routine $routine)
    {
        abort_unless($routine->user_id === auth()->id(), 403);

        $routine->load([
            'exercises.exercise.translations',
            'exercises.sets'
        ]);

        return view('routines.show', compact('routine'));
    }
}