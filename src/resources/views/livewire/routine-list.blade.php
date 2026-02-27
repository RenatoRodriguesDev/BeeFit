<div class="space-y-6">
    @if($routines->isEmpty())
        <div class="bg-zinc-600 rounded-3xl p-8 text-zinc-400">
            {{ __('app.get_started_start_by_creating_a_routine') }}
        </div>
    @else
        @foreach($routines as $routine)

            <a href="{{ route('routines.show', $routine) }}"
                class="block bg-zinc-600 p-5 md:p-6 rounded-2xl hover:bg-zinc-800 transition">

                <div class="flex justify-between items-center">
                    <button wire:click.prevent="startWorkout({{ $routine->id }})" class="bg-white text-black 
                   px-6 py-3 rounded-2xl transition">
                        {{ __('app.start_workout') }}
                    </button>
                    <h2 class="text-xl font-semibold">
                        {{ $routine->name }}
                    </h2>

                    <div class="flex items-center gap-4">

                        <span class="text-zinc-500 text-sm">
                            {{ $routine->exercises_count }} {{ __('app.exercises') }}
                        </span>

                        <button wire:click.prevent="confirmDelete({{ $routine->id }})"
                            class="text-red-500 hover:text-red-400 transition">
                            {{ __('app.delete') }}
                        </button>

                    </div>
                </div>

            </a>

        @endforeach
    @endif

    @if($showDeleteModal)

        <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
            <div class="bg-zinc-600 p-6 rounded-2xl w-96 space-y-6">

                <div>
                    <h2 class="text-xl font-semibold">
                        {{ __('app.confirm_delete') }}
                    </h2>

                    <p class="text-zinc-400 mt-2">
                        {{ __('app.confirm_delete_message') }}
                    </p>
                </div>

                <div class="flex justify-end gap-3">

                    <button wire:click="closeDeleteModal"
                        class="px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 transition">
                        {{ __('app.cancel') }}
                    </button>

                    <button wire:click="deleteRoutine" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-500 transition">
                        {{ __('app.delete') }}
                    </button>

                </div>
            </div>
        </div>

    @endif

</div>