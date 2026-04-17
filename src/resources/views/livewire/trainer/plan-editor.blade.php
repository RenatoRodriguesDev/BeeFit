<div class="space-y-8">

    {{-- Header --}}
    <div class="flex items-start gap-4">
        <a href="{{ route('trainer.plans') }}"
           class="mt-1 text-zinc-500 hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>

        <div class="flex-1">
            @if($editingMeta)
                <div class="space-y-3">
                    <input wire:model="name" type="text"
                           class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-2.5 text-lg font-bold text-white focus:outline-none focus:border-yellow-500/60">
                    <textarea wire:model="description" rows="2"
                              placeholder="{{ __('app.description') }}"
                              class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-yellow-500/60 resize-none"></textarea>
                    <div class="flex gap-2">
                        <button wire:click="saveMeta"
                                class="text-sm font-semibold bg-yellow-500 hover:bg-yellow-400 text-zinc-900 px-4 py-2 rounded-xl transition">
                            {{ __('app.save') }}
                        </button>
                        <button wire:click="$set('editingMeta', false)"
                                class="text-sm text-zinc-400 hover:text-white px-4 py-2 rounded-xl hover:bg-zinc-800 transition">
                            {{ __('app.cancel') }}
                        </button>
                    </div>
                </div>
            @else
                <div class="flex items-start gap-3">
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-white">{{ $trainerPlan->name }}</h1>
                        @if($trainerPlan->description)
                            <p class="text-sm text-zinc-400 mt-1">{{ $trainerPlan->description }}</p>
                        @endif
                    </div>
                    <button wire:click="$set('editingMeta', true)"
                            class="text-zinc-500 hover:text-white transition mt-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Two-column layout --}}
    <div class="grid lg:grid-cols-3 gap-8">

        {{-- Left: Routines by week --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-white">{{ __('app.trainer_plan_routines') }}</h2>
                <button wire:click="$set('showAddRoutineModal', true)"
                        class="flex items-center gap-1.5 text-sm font-medium text-yellow-500 hover:text-yellow-400 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('app.trainer_add_routine') }}
                </button>
            </div>

            @if($planRoutines->isEmpty())
                <div class="border-2 border-dashed border-zinc-800 rounded-2xl p-10 text-center text-zinc-500">
                    <p class="text-sm">{{ __('app.trainer_no_plan_routines') }}</p>
                    <button wire:click="$set('showAddRoutineModal', true)"
                            class="mt-3 text-yellow-500 hover:text-yellow-400 text-sm font-medium transition">
                        {{ __('app.trainer_add_first_routine') }}
                    </button>
                </div>
            @else
                @foreach($planRoutines as $week => $entries)
                    <div>
                        <h3 class="text-xs font-semibold text-zinc-500 uppercase tracking-widest mb-3">
                            {{ __('app.trainer_week') }} {{ $week }}
                        </h3>
                        <div class="space-y-2">
                            @foreach($entries as $entry)
                                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 flex items-center gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            @if($entry->day_label)
                                                <span class="text-xs bg-yellow-500/15 text-yellow-400 px-2 py-0.5 rounded-full font-medium">
                                                    {{ __('app.day_' . $entry->day_label) }}
                                                </span>
                                            @endif
                                            <p class="text-sm font-semibold text-white truncate">
                                                {{ $entry->routine?->name ?? '—' }}
                                            </p>
                                        </div>
                                        <p class="text-xs text-zinc-500 mt-0.5">
                                            {{ $entry->routine?->exercises_count ?? 0 }} {{ __('app.exercises') }}
                                            @if($entry->notes)
                                                · {{ $entry->notes }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1 shrink-0">
                                        <button wire:click="editEntry({{ $entry->id }})"
                                                class="text-zinc-500 hover:text-yellow-400 transition p-1 rounded-lg hover:bg-zinc-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="removeRoutineEntry({{ $entry->id }})"
                                                class="text-zinc-600 hover:text-red-400 transition p-1 rounded-lg hover:bg-zinc-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Right: Assigned clients --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-white">{{ __('app.trainer_assigned_clients') }}</h2>
                @if($activeClients->isNotEmpty())
                    <button wire:click="$set('showAssignModal', true)"
                            class="text-sm font-medium text-yellow-500 hover:text-yellow-400 transition">
                        + {{ __('app.assign') }}
                    </button>
                @endif
            </div>

            @if($assignments->isEmpty())
                <div class="border-2 border-dashed border-zinc-800 rounded-2xl p-6 text-center text-zinc-500">
                    <p class="text-sm">{{ __('app.trainer_no_assignments') }}</p>
                    @if($activeClients->isNotEmpty())
                        <button wire:click="$set('showAssignModal', true)"
                                class="mt-2 text-yellow-500 hover:text-yellow-400 text-sm font-medium transition">
                            {{ __('app.trainer_assign_client') }}
                        </button>
                    @else
                        <p class="text-xs mt-1">{{ __('app.trainer_need_active_clients') }}</p>
                    @endif
                </div>
            @else
                <div class="space-y-2">
                    @foreach($assignments as $assignment)
                        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-3 flex items-center gap-3">
                            @if($assignment->client->avatarUrl())
                                <img src="{{ $assignment->client->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover shrink-0">
                            @else
                                <div class="w-8 h-8 rounded-full bg-zinc-700 flex items-center justify-center text-xs font-bold text-zinc-300 shrink-0">
                                    {{ $assignment->client->initials() }}
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-white truncate">{{ $assignment->client->name }}</p>
                                <p class="text-xs text-zinc-500">@if($assignment->client->username){{ '@' . $assignment->client->username }}@endif</p>
                            </div>
                            <button wire:click="unassignClient({{ $assignment->client_id }})"
                                    class="text-zinc-600 hover:text-red-400 transition shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Add Routine Modal --}}
    @if($showAddRoutineModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm" wire:click.self="$set('showAddRoutineModal', false)">
            <div class="bg-zinc-900 border border-zinc-700 rounded-2xl p-6 w-full max-w-md mx-4">
                <h2 class="text-lg font-bold text-white mb-5">{{ __('app.trainer_add_routine') }}</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-zinc-400 mb-1.5">{{ __('app.routine') }}</label>
                        <select wire:model="selectedRoutineId"
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-yellow-500/60">
                            <option value="0">{{ __('app.select_routine') }}</option>
                            @foreach($myRoutines as $routine)
                                <option value="{{ $routine->id }}">{{ $routine->name }} ({{ $routine->exercises_count }} {{ __('app.exercises') }})</option>
                            @endforeach
                        </select>
                        @error('selectedRoutineId') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-zinc-400 mb-1.5">{{ __('app.trainer_week') }}</label>
                            <input wire:model="weekNumber" type="number" min="1" max="52"
                                   class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-yellow-500/60">
                            @error('weekNumber') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-zinc-400 mb-1.5">{{ __('app.trainer_day') }} <span class="text-zinc-600">({{ __('app.optional') }})</span></label>
                            <select wire:model="dayLabel"
                                    class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-yellow-500/60">
                                <option value="">—</option>
                                @foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)
                                    <option value="{{ $day }}">{{ __('app.day_' . $day) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-400 mb-1.5">{{ __('app.notes') }} <span class="text-zinc-600">({{ __('app.optional') }})</span></label>
                        <input wire:model="notes" type="text" placeholder="{{ __('app.trainer_notes_placeholder') }}"
                               class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-yellow-500/60">
                    </div>
                </div>

                <div class="flex gap-3 mt-5">
                    <button wire:click="$set('showAddRoutineModal', false)"
                            class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm font-medium py-2.5 rounded-xl transition">
                        {{ __('app.cancel') }}
                    </button>
                    <button wire:click="addRoutine"
                            class="flex-1 bg-yellow-500 hover:bg-yellow-400 text-zinc-900 text-sm font-semibold py-2.5 rounded-xl transition">
                        {{ __('app.add') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Edit Routine Entry Modal --}}
    @if($showEditEntryModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm" wire:click.self="$set('showEditEntryModal', false)">
            <div class="bg-zinc-900 border border-zinc-700 rounded-2xl p-6 w-full max-w-md mx-4">
                <h2 class="text-lg font-bold text-white mb-5">{{ __('app.edit') }}</h2>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-zinc-400 mb-1.5">{{ __('app.trainer_week') }}</label>
                            <input wire:model.live="editWeekNumber" type="number" min="1" max="52"
                                   class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-yellow-500/60">
                            @error('editWeekNumber') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-zinc-400 mb-1.5">{{ __('app.trainer_day') }} <span class="text-zinc-600">({{ __('app.optional') }})</span></label>
                            <select wire:model.live="editDayLabel"
                                    class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-yellow-500/60">
                                <option value="">—</option>
                                @foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)
                                    <option value="{{ $day }}">{{ __('app.day_' . $day) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-400 mb-1.5">{{ __('app.notes') }} <span class="text-zinc-600">({{ __('app.optional') }})</span></label>
                        <input wire:model.live="editNotes" type="text" placeholder="{{ __('app.trainer_notes_placeholder') }}"
                               class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-yellow-500/60">
                    </div>
                </div>

                <div class="flex gap-3 mt-5">
                    <button wire:click="$set('showEditEntryModal', false)"
                            class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm font-medium py-2.5 rounded-xl transition">
                        {{ __('app.cancel') }}
                    </button>
                    <button wire:click="updateEntry"
                            class="flex-1 bg-yellow-500 hover:bg-yellow-400 text-zinc-900 text-sm font-semibold py-2.5 rounded-xl transition">
                        {{ __('app.save') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Assign to Client Modal --}}
    @if($showAssignModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm" wire:click.self="$set('showAssignModal', false)">
            <div class="bg-zinc-900 border border-zinc-700 rounded-2xl p-6 w-full max-w-sm mx-4">
                <h2 class="text-lg font-bold text-white mb-5">{{ __('app.trainer_assign_client') }}</h2>

                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1.5">{{ __('app.trainer_select_client') }}</label>
                    <select wire:model="selectedClientId"
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-yellow-500/60">
                        <option value="0">{{ __('app.select') }}</option>
                        @foreach($activeClients as $tc)
                            <option value="{{ $tc->client_id }}">{{ $tc->client->name }}{{ $tc->client->username ? ' (@' . $tc->client->username . ')' : '' }}</option>
                        @endforeach
                    </select>
                    @error('selectedClientId') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 mt-5">
                    <button wire:click="$set('showAssignModal', false)"
                            class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm font-medium py-2.5 rounded-xl transition">
                        {{ __('app.cancel') }}
                    </button>
                    <button wire:click="assignToClient"
                            class="flex-1 bg-yellow-500 hover:bg-yellow-400 text-zinc-900 text-sm font-semibold py-2.5 rounded-xl transition">
                        {{ __('app.assign') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
