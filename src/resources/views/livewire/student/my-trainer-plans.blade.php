<div class="space-y-8">

    <div>
        <h1 class="text-2xl font-bold text-white">{{ __('app.my_trainer') }}</h1>
        <p class="text-sm text-zinc-400 mt-0.5">{{ __('app.my_trainer_subtitle') }}</p>
    </div>

    {{-- Pending invites --}}
    @foreach($pendingInvites as $invite)
        <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-2xl p-5 flex items-center gap-4">
            @if($invite->trainer->avatarUrl())
                <img src="{{ $invite->trainer->avatarUrl() }}" class="w-12 h-12 rounded-full object-cover shrink-0">
            @else
                <div class="w-12 h-12 rounded-full bg-zinc-700 flex items-center justify-center text-base font-bold text-zinc-300 shrink-0">
                    {{ $invite->trainer->initials() }}
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white">
                    {{ __('app.trainer_invite_from', ['name' => $invite->trainer->name]) }}
                </p>
                <p class="text-xs text-zinc-400 mt-0.5">@if($invite->trainer->username){{ '@' . $invite->trainer->username }}@endif</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button wire:click="rejectInvite({{ $invite->id }})"
                        class="text-sm text-zinc-400 hover:text-red-400 px-3 py-2 rounded-xl hover:bg-zinc-800 transition">
                    {{ __('app.reject') }}
                </button>
                <button wire:click="acceptInvite({{ $invite->id }})"
                        class="text-sm font-semibold bg-yellow-500 hover:bg-yellow-400 text-zinc-900 px-4 py-2 rounded-xl transition">
                    {{ __('app.accept') }}
                </button>
            </div>
        </div>
    @endforeach

    {{-- No trainer --}}
    @if(! $trainerRelation && $pendingInvites->isEmpty())
        <div class="text-center py-20 text-zinc-500">
            <svg class="w-14 h-14 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <p class="text-sm font-medium">{{ __('app.my_trainer_none') }}</p>
            <p class="text-xs text-zinc-600 mt-1">{{ __('app.my_trainer_none_desc') }}</p>
        </div>
    @endif

    {{-- Active trainer info --}}
    @if($trainerRelation)
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 flex items-center gap-4">
            @if($trainerRelation->trainer->avatarUrl())
                <img src="{{ $trainerRelation->trainer->avatarUrl() }}" class="w-12 h-12 rounded-full object-cover shrink-0">
            @else
                <div class="w-12 h-12 rounded-full bg-yellow-500/20 flex items-center justify-center text-base font-bold text-yellow-400 shrink-0">
                    {{ $trainerRelation->trainer->initials() }}
                </div>
            @endif
            <div class="flex-1">
                <p class="text-xs text-zinc-500 uppercase tracking-widest mb-0.5">{{ __('app.my_trainer') }}</p>
                <p class="text-base font-semibold text-white">{{ $trainerRelation->trainer->name }}</p>
                <p class="text-xs text-zinc-400">@if($trainerRelation->trainer->username){{ '@' . $trainerRelation->trainer->username }}@endif</p>
            </div>
            <button wire:click="$set('showLeaveModal', true)"
                    class="shrink-0 text-xs text-zinc-500 hover:text-red-400 px-3 py-2 rounded-xl hover:bg-zinc-800 transition">
                {{ __('app.trainer_leave') }}
            </button>
        </div>

        {{-- Leave confirmation modal --}}
        @if($showLeaveModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm" wire:click.self="$set('showLeaveModal', false)">
                <div class="bg-zinc-900 border border-zinc-700 rounded-2xl p-6 w-full max-w-sm mx-4">
                    <h2 class="text-lg font-bold text-white mb-2">{{ __('app.trainer_leave_title') }}</h2>
                    <p class="text-sm text-zinc-400 mb-5">{{ __('app.trainer_leave_desc') }}</p>
                    <div class="flex gap-3">
                        <button wire:click="$set('showLeaveModal', false)"
                                class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm font-medium py-2.5 rounded-xl transition">
                            {{ __('app.cancel') }}
                        </button>
                        <button wire:click="leaveTrainer"
                                class="flex-1 bg-red-600 hover:bg-red-500 text-white text-sm font-semibold py-2.5 rounded-xl transition">
                            {{ __('app.trainer_leave') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Assigned plans --}}
    @if($trainerRelation && count($assignments) > 0)
        <div class="space-y-6">
            @foreach($assignments as $assignment)
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
                    {{-- Plan header --}}
                    <div class="px-5 py-4 border-b border-zinc-800">
                        <h2 class="text-base font-bold text-white">{{ $assignment->plan->name }}</h2>
                        @if($assignment->plan->description)
                            <p class="text-sm text-zinc-400 mt-0.5">{{ $assignment->plan->description }}</p>
                        @endif
                    </div>

                    {{-- Routines grouped by week --}}
                    @php
                        $byWeek = $assignment->plan->planRoutines->groupBy('week_number');
                    @endphp

                    <div class="divide-y divide-zinc-800">
                        @foreach($byWeek as $week => $entries)
                            <div class="px-5 py-4">
                                <h3 class="text-xs font-semibold text-zinc-500 uppercase tracking-widest mb-3">
                                    {{ __('app.trainer_week') }} {{ $week }}
                                </h3>
                                <div class="space-y-2">
                                    @foreach($entries as $entry)
                                        @if($entry->routine)
                                            <div x-data="{ open: false, videoUrl: null }" class="bg-zinc-800/50 rounded-xl overflow-hidden">

                                                {{-- Routine header row --}}
                                                <div class="flex items-center gap-3 p-3">
                                                    <button @click="open = !open" class="flex-1 flex items-center gap-2 text-left min-w-0">
                                                        <svg class="w-4 h-4 text-zinc-500 shrink-0 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                                        </svg>
                                                        <div class="flex items-center gap-2 min-w-0">
                                                            @if($entry->day_label)
                                                                <span class="text-xs bg-yellow-500/15 text-yellow-400 px-2 py-0.5 rounded-full font-medium shrink-0">
                                                                    {{ __('app.day_' . strtolower($entry->day_label)) }}
                                                                </span>
                                                            @endif
                                                            <p class="text-sm font-medium text-white truncate">{{ $entry->routine->name }}</p>
                                                        </div>
                                                        <p class="text-xs text-zinc-500 shrink-0">
                                                            {{ $entry->routine->exercises_count }} {{ __('app.exercises') }}
                                                            @if($entry->notes) · {{ $entry->notes }} @endif
                                                        </p>
                                                    </button>

                                                    <button wire:click="startWorkoutFromPlan({{ $entry->routine_id }}, {{ $assignment->id }})"
                                                            wire:loading.attr="disabled"
                                                            class="shrink-0 flex items-center gap-1.5 bg-yellow-500 hover:bg-yellow-400 disabled:opacity-50 text-zinc-900 text-xs font-semibold px-3 py-2 rounded-xl transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/>
                                                        </svg>
                                                        {{ __('app.start_workout') }}
                                                    </button>
                                                </div>

                                                {{-- Expandable exercise list --}}
                                                <div x-show="open" x-transition class="border-t border-zinc-700/50">
                                                    @forelse($entry->routine->exercises as $re)
                                                        @php $ex = $re->exercise; @endphp
                                                        @if($ex)
                                                        <div class="flex items-center gap-3 px-4 py-3 border-b border-zinc-700/30 last:border-0">

                                                            {{-- Thumbnail --}}
                                                            <div class="w-10 h-10 rounded-lg overflow-hidden bg-zinc-700 shrink-0">
                                                                @if($ex->thumbnail_url)
                                                                    <img src="{{ $ex->thumbnail_url }}" class="w-full h-full object-cover" alt="">
                                                                @else
                                                                    <div class="w-full h-full flex items-center justify-center text-zinc-500">
                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            {{-- Name + muscle --}}
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-sm font-medium text-white truncate">{{ $ex->translate()?->name ?? '—' }}</p>
                                                                @if($ex->primaryMuscle)
                                                                    <p class="text-xs text-zinc-500">{{ $ex->primaryMuscle->translate()?->name ?? '—' }}</p>
                                                                @endif
                                                            </div>

                                                            {{-- Sets summary --}}
                                                            <span class="text-xs text-zinc-500 shrink-0">
                                                                {{ $re->sets->count() }} {{ __('app.sets') }}
                                                            </span>

                                                            {{-- Video button --}}
                                                            @if($ex->has_video)
                                                                <button @click="videoUrl = '{{ asset($ex->video_path) }}'"
                                                                        class="shrink-0 text-zinc-500 hover:text-yellow-400 transition p-1.5 rounded-lg hover:bg-zinc-700">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                        @endif
                                                    @empty
                                                        <p class="text-xs text-zinc-500 px-4 py-3">{{ __('app.no_exercises') }}</p>
                                                    @endforelse
                                                </div>

                                                {{-- Video lightbox --}}
                                                <template x-teleport="body">
                                                    <div x-show="videoUrl" x-cloak
                                                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
                                                         @click.self="videoUrl = null" @keydown.escape.window="videoUrl = null">
                                                        <div class="w-full max-w-2xl">
                                                            <div class="flex justify-end mb-2">
                                                                <button @click="videoUrl = null" class="text-zinc-400 hover:text-white transition">
                                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                </button>
                                                            </div>
                                                            <video x-bind:src="videoUrl" controls autoplay
                                                                   class="w-full rounded-2xl bg-black max-h-[70vh]">
                                                            </video>
                                                        </div>
                                                    </div>
                                                </template>

                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @elseif($trainerRelation)
        <div class="text-center py-12 text-zinc-500">
            <p class="text-sm">{{ __('app.my_trainer_no_plans') }}</p>
        </div>
    @endif

</div>
