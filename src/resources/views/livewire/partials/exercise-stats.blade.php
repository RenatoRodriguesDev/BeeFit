@php
    $pr = \App\Models\PersonalRecord::where('user_id', auth()->id())
        ->where('exercise_id', $exercise->id)
        ->with('workout')
        ->first();
@endphp

@if(!$pr)
    <div class="flex flex-col items-center py-10 text-center">
        <div class="w-14 h-14 rounded-2xl bg-zinc-800/60 flex items-center justify-center mb-4">
            <svg class="w-7 h-7 text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
            </svg>
        </div>
        <p class="text-sm text-zinc-500 font-medium">{{ __('app.no_records_yet') }}</p>
        <p class="text-xs text-zinc-600 mt-1">{{ __('app.complete_workout_to_set_pr') }}</p>
    </div>
@else
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-zinc-800/60 border border-zinc-800 rounded-xl p-4">
            <div class="text-[10px] text-zinc-500 uppercase tracking-wider mb-2">{{ __('app.pr_max_weight') }}</div>
            <div class="text-2xl font-bold text-white">{{ $pr->max_weight }}<span class="text-sm font-normal text-zinc-500 ml-1">kg</span></div>
            <div class="text-xs text-zinc-500 mt-1">x {{ $pr->reps_at_max_weight }} reps</div>
        </div>
        <div class="bg-zinc-800/60 border border-zinc-800 rounded-xl p-4">
            <div class="text-[10px] text-zinc-500 uppercase tracking-wider mb-2">{{ __('app.pr_1rm') }}</div>
            <div class="text-2xl font-bold text-amber-400">{{ number_format($pr->estimated_1rm, 1) }}<span class="text-sm font-normal text-zinc-500 ml-1">kg</span></div>
            <div class="text-xs text-zinc-500 mt-1">Epley</div>
        </div>
        <div class="bg-zinc-800/60 border border-zinc-800 rounded-xl p-4">
            <div class="text-[10px] text-zinc-500 uppercase tracking-wider mb-2">{{ __('app.pr_max_reps') }}</div>
            <div class="text-2xl font-bold text-white">{{ $pr->max_reps }}<span class="text-sm font-normal text-zinc-500 ml-1">reps</span></div>
            <div class="text-xs text-zinc-500 mt-1">@ {{ $pr->weight_at_max_reps }} kg</div>
        </div>
        @if($pr->max_volume_set)
        <div class="bg-zinc-800/60 border border-zinc-800 rounded-xl p-4">
            <div class="text-[10px] text-zinc-500 uppercase tracking-wider mb-2">{{ __('app.pr_max_volume') }}</div>
            <div class="text-2xl font-bold text-white">{{ number_format($pr->max_volume_set) }}<span class="text-sm font-normal text-zinc-500 ml-1">kg</span></div>
            <div class="text-xs text-zinc-500 mt-1">{{ __('app.pr_single_set') }}</div>
        </div>
        @endif
    </div>
    @if($pr->workout)
        <p class="text-xs text-zinc-600 text-right mt-3">
            {{ __('app.pr_achieved_on') }} {{ $pr->workout->started_at->format('d M Y') }}
        </p>
    @endif
@endif
