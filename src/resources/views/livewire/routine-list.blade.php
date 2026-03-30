<div class="space-y-3">

    @if($routines->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="text-5xl mb-4">🏋️</div>
            <p class="text-zinc-400 text-sm">{{ __('app.get_started_start_by_creating_a_routine') }}</p>
        </div>
    @else
        @foreach($routines as $routine)
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">

                <div class="flex items-center gap-4 px-5 py-4">

                    {{-- Ícone --}}
                    <div class="w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center shrink-0 text-lg">
                        💪
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="font-semibold text-white truncate">{{ $routine->name }}</h2>
                            @if($routine->is_active)
                                <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 border border-green-500/20">
                                    {{ __('app.active') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-zinc-500 mt-0.5">
                            {{ $routine->exercises_count }} {{ __('app.exercises') }}
                        </p>
                    </div>

                    {{-- Acções --}}
                    <div class="flex items-center gap-2 shrink-0">

                        <button wire:click="startWorkout({{ $routine->id }})"
                            class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-green-500 hover:bg-green-400 text-black text-xs font-semibold transition">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            {{ __('app.start_workout') }}
                        </button>

                        <a href="{{ route('routines.show', $routine) }}"
                            class="p-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white transition"
                            title="{{ __('app.edit') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>

                        <button wire:click="confirmDelete({{ $routine->id }})"
                            class="p-2 rounded-xl bg-zinc-800 hover:bg-red-500/10 text-zinc-600 hover:text-red-400 transition"
                            title="{{ __('app.delete') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>

                    </div>

                </div>

            </div>
        @endforeach
    @endif

    {{-- Modal confirmar delete --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 w-full max-w-sm space-y-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('app.confirm_delete') }}</h2>
                    <p class="text-sm text-zinc-400 mt-1">{{ __('app.confirm_delete_message') }}</p>
                </div>
                <div class="flex gap-3">
                    <button wire:click="closeDeleteModal"
                        class="flex-1 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-sm transition">
                        {{ __('app.cancel') }}
                    </button>
                    <button wire:click="deleteRoutine"
                        class="flex-1 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-sm font-semibold transition">
                        {{ __('app.delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
