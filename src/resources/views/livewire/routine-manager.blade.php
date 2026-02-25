<div>

    <button
        wire:click="$set('showModal', true)"
        class="block w-full bg-white text-black text-center py-3 rounded-xl font-semibold">
        {{ __('app.new_routine') }}
    </button>

    @if($showModal)
        <div
            class="fixed inset-0 bg-black/70 flex items-center justify-center z-[9999]"
            style="position: fixed;">

            <div class="bg-zinc-900 p-6 rounded-2xl w-96 space-y-4">

                <h2 class="text-lg font-semibold">
                    New Routine
                </h2>

                <input
                    wire:model="name"
                    type="text"
                    class="w-full bg-zinc-800 p-3 rounded-xl"
                    placeholder="Push Day">

                <div class="flex justify-end gap-3">
                    <button wire:click="$set('showModal', false)">
                        Cancel
                    </button>

                    <button
                        wire:click="createRoutine"
                        class="bg-white text-black px-4 py-2 rounded-xl">
                        Create
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>