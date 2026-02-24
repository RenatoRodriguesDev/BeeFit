<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exercise;

class LibraryController extends Controller
{
    public function index(Request $request, $locale)
    {
        app()->setLocale($locale);

        $equipmentId = $request->equipment;
        $muscleId = $request->muscle;
        $search = $request->search;

        $exercises = Exercise::query()
            ->with(['translations', 'equipment.translations', 'primaryMuscle.translations'])
            ->where(function ($q) {
                $q->whereNull('created_by')
                    ->orWhere('created_by', auth()->id());
            })
            ->when(
                $equipmentId,
                fn($q) =>
                $q->where('equipment_id', $equipmentId)
            )
            ->when(
                $muscleId,
                fn($q) =>
                $q->where('primary_muscle_id', $muscleId)
            )
            ->when(
                $search,
                fn($q) =>
                $q->whereHas('translations', function ($q) use ($search) {
                    $q->where('locale', app()->getLocale())
                        ->where('name', 'like', "%{$search}%");
                })
            )
            ->paginate(20);

        return view('library.index', compact('exercises'));
    }
}
