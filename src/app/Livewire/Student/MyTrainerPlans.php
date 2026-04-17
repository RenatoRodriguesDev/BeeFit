<?php

namespace App\Livewire\Student;

use App\Models\TrainerClient;
use App\Models\TrainerPlanAssignment;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutSet;
use App\Models\Routine;
use Livewire\Component;

class MyTrainerPlans extends Component
{
    public bool $showLeaveModal = false;

    public function leaveTrainer(): void
    {
        TrainerClient::where('client_id', auth()->id())
            ->whereIn('status', ['active', 'invited'])
            ->delete();

        $this->showLeaveModal = false;
        $this->dispatch('toast', message: __('app.trainer_left'), type: 'success');
    }

    public function acceptInvite(int $trainerClientId): void
    {
        $tc = TrainerClient::where('id', $trainerClientId)
            ->where('client_id', auth()->id())
            ->where('status', 'invited')
            ->firstOrFail();

        $tc->update([
            'status'      => 'active',
            'accepted_at' => now(),
        ]);

        $this->dispatch('toast', message: __('app.trainer_invite_accepted'), type: 'success');
    }

    public function rejectInvite(int $trainerClientId): void
    {
        TrainerClient::where('id', $trainerClientId)
            ->where('client_id', auth()->id())
            ->where('status', 'invited')
            ->update(['status' => 'rejected']);

        $this->dispatch('toast', message: __('app.trainer_invite_rejected'), type: 'info');
    }

    public function startWorkoutFromPlan(int $routineId, int $assignmentId): mixed
    {
        // Verify the assignment belongs to this client and is active
        $assignment = TrainerPlanAssignment::where('id', $assignmentId)
            ->where('client_id', auth()->id())
            ->where('is_active', true)
            ->firstOrFail();

        // Verify the routine is part of the assigned plan
        $planRoutine = $assignment->plan->planRoutines()
            ->where('routine_id', $routineId)
            ->first();

        abort_if(! $planRoutine, 403);

        // Load the trainer's routine
        $routine = Routine::with('exercises.sets')
            ->findOrFail($routineId);

        // Check for already active workout
        $activeWorkout = Workout::where('user_id', auth()->id())
            ->whereIn('status', ['active', 'paused'])
            ->first();

        if ($activeWorkout) {
            return redirect()->route('workouts.session', $activeWorkout);
        }

        // Create workout as the student (not the trainer)
        $workout = Workout::create([
            'user_id'    => auth()->id(),
            'routine_id' => $routine->id,
            'started_at' => now(),
            'status'     => 'active',
        ]);

        foreach ($routine->exercises as $routineExercise) {
            $workoutExercise = WorkoutExercise::create([
                'workout_id'  => $workout->id,
                'exercise_id' => $routineExercise->exercise_id,
                'order'       => $routineExercise->order,
            ]);

            foreach ($routineExercise->sets as $set) {
                WorkoutSet::create([
                    'workout_exercise_id' => $workoutExercise->id,
                    'set_number'          => $set->set_number,
                    'weight'              => $set->weight,
                    'reps'                => $set->reps,
                ]);
            }
        }

        return redirect()->route('workouts.session', $workout);
    }

    public function render()
    {
        $user = auth()->user();

        // Pending invites
        $pendingInvites = TrainerClient::where('client_id', $user->id)
            ->where('status', 'invited')
            ->with('trainer')
            ->get();

        // Active trainer relationship
        $trainerRelation = TrainerClient::where('client_id', $user->id)
            ->where('status', 'active')
            ->with('trainer')
            ->first();

        // Assigned plans (only if active client)
        $assignments = [];
        if ($trainerRelation) {
            $assignments = TrainerPlanAssignment::where('client_id', $user->id)
                ->where('is_active', true)
                ->with([
                    'plan.planRoutines' => fn($q) => $q->orderBy('week_number')->orderBy('order'),
                    'plan.planRoutines.routine' => fn($q) => $q->withCount('exercises'),
                    'plan.planRoutines.routine.exercises' => fn($q) => $q->with([
                        'exercise.translations',
                        'exercise.primaryMuscle.translations',
                        'sets',
                    ]),
                    'plan.trainer',
                ])
                ->get();
        }

        return view('livewire.student.my-trainer-plans', compact(
            'pendingInvites', 'trainerRelation', 'assignments'
        ))->title(__('app.my_trainer'));
    }
}
