<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Routine;

class DashboardController extends Controller
{
    public function index()
    {
        $routine = Routine::where('user_id', auth()->id())
            ->where('is_active', true)
            ->with('exercises.sets')
            ->first();

        $exerciseCount = $routine?->exercises->count() ?? 0;

        $volume = 0;

        if ($routine) {
            foreach ($routine->exercises as $exercise) {
                foreach ($exercise->sets as $set) {
                    $volume += ($set->weight ?? 0) * ($set->reps ?? 0);
                }
            }
        }

        return view('dashboard', compact(
            'routine',
            'exerciseCount',
            'volume'
        ));
    }
}