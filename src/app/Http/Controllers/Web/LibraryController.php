<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\Muscle;

class LibraryController extends Controller
{
    public function index(Request $request)
{
    $query = Exercise::query()
        ->with([
            'translations',
            'equipment.translations',
            'primaryMuscle.translations'
        ]);

    if ($request->equipment) {
        $query->where('equipment_id', $request->equipment);
    }

    if ($request->muscle) {
        $query->where('primary_muscle_id', $request->muscle);
    }

    if ($request->search) {
        $query->whereHas('translations', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->where('locale', app()->getLocale());
        });
    }

    $exercises = $query->paginate(12);

    $equipment = Equipment::with('translations')->get();
    $muscles = Muscle::with('translations')->get();

    return view('library.index', compact(
        'exercises',
        'equipment',
        'muscles'
    ));
}
}