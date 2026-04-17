<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ __('app.trainer_plans') }}</h1>
            <p class="text-sm text-zinc-400 mt-0.5">{{ __('app.trainer_plans_subtitle') }}</p>
        </div>
        <button wire:click="$set('showCreateModal', true)"
                class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-400 text-zinc-900 font-semibold text-sm px-4 py-2 rounded-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('app.trainer_create_plan') }}
        </button>
    </div>

    {{-- Tabs / Nav --}}
    <div class="flex gap-3 mb-6">
        <a href="{{ route('trainer.clients') }}"
           class="px-4 py-2 rounded-xl text-sm font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition">
            {{ __('app.trainer_clients') }}
        </a>
        <a href="{{ route('trainer.plans') }}"
           class="px-4 py-2 rounded-xl text-sm font-medium bg-zinc-800 text-white">
            {{ __('app.trainer_plans') }}
        </a>
    </div>

    {{-- Plan grid --}}
    @if($plans->isEmpty())
        <div class="text-center py-16 text-zinc-500">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm">{{ __('app.trainer_no_plans') }}</p>
            <button wire:click="$set('showCreateModal', true)"
                    class="mt-4 text-yellow-500 hover:text-yellow-400 text-sm font-medium transition">
                {{ __('app.trainer_create_first_plan') }}
            </button>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($plans as $plan)
                <div class="bg-zinc-900 border border-zinc-800 hover:border-zinc-700 rounded-2xl p-5 flex flex-col gap-4 transition">
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-white truncate">{{ $plan->name }}</h3>
                        @if($plan->description)
                            <p class="text-sm text-zinc-400 mt-1 line-clamp-2">{{ $plan->description }}</p>
                        @endif

                        <div class="flex items-center gap-4 mt-3 text-xs text-zinc-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                {{ $plan->plan_routines_count }} {{ __('app.routines') }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $plan->active_assignments_count }} {{ __('app.trainer_assigned_clients') }}
                            </span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('trainer.plans.edit', $plan) }}"
                           class="flex-1 text-center text-sm font-medium bg-zinc-800 hover:bg-zinc-700 text-white py-2 rounded-xl transition">
                            {{ __('app.edit') }}
                        </a>
                        <button wire:click="confirmDelete({{ $plan->id }})"
                                class="text-sm text-zinc-500 hover:text-red-400 bg-zinc-800 hover:bg-zinc-800 px-3 py-2 rounded-xl transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Create modal --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm" wire:click.self="$set('showCreateModal', false)">
            <div class="bg-zinc-900 border border-zinc-700 rounded-2xl p-6 w-full max-w-md mx-4">
                <h2 class="text-lg font-bold text-white mb-5">{{ __('app.trainer_create_plan') }}</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-zinc-400 mb-1.5">{{ __('app.name') }}</label>
                        <input wire:model="planName"
                               type="text"
                               placeholder="{{ __('app.trainer_plan_name_placeholder') }}"
                               class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-yellow-500/60">
                        @error('planName')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-400 mb-1.5">{{ __('app.description') }} <span class="text-zinc-600">({{ __('app.optional') }})</span></label>
                        <textarea wire:model="planDescription"
                                  rows="3"
                                  placeholder="{{ __('app.trainer_plan_desc_placeholder') }}"
                                  class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-yellow-500/60 resize-none"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-5">
                    <button wire:click="$set('showCreateModal', false)"
                            class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm font-medium py-2.5 rounded-xl transition">
                        {{ __('app.cancel') }}
                    </button>
                    <button wire:click="createPlan"
                            class="flex-1 bg-yellow-500 hover:bg-yellow-400 text-zinc-900 text-sm font-semibold py-2.5 rounded-xl transition">
                        {{ __('app.create') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm" wire:click.self="$set('showDeleteModal', false)">
            <div class="bg-zinc-900 border border-zinc-700 rounded-2xl p-6 w-full max-w-sm mx-4">
                <h2 class="text-lg font-bold text-white mb-2">{{ __('app.trainer_delete_plan_title') }}</h2>
                <p class="text-sm text-zinc-400 mb-5">{{ __('app.trainer_delete_plan_desc') }}</p>
                <div class="flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm font-medium py-2.5 rounded-xl transition">
                        {{ __('app.cancel') }}
                    </button>
                    <button wire:click="deletePlan"
                            class="flex-1 bg-red-600 hover:bg-red-500 text-white text-sm font-semibold py-2.5 rounded-xl transition">
                        {{ __('app.delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
