<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Exercícios</h1>
            <p class="text-sm text-zinc-500 mt-1">Cria e gere a biblioteca de exercícios</p>
        </div>
        <button wire:click="openCreate"
            class="px-4 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-semibold text-white transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Exercício
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <input wire:model.live.debounce.300ms="search"
               type="text" placeholder="Pesquisar por nome..."
               class="flex-1 bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">

        <select wire:model.live="filterEquipment"
                class="bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
            <option value="">Todo o equipamento</option>
            @foreach($equipmentList as $eq)
                <option value="{{ $eq->id }}">{{ $eq->translate('en')?->name ?? $eq->id }}</option>
            @endforeach
        </select>

        <select wire:model.live="filterMuscle"
                class="bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
            <option value="">Todos os músculos</option>
            @foreach($muscleList as $m)
                <option value="{{ $m->id }}">{{ $m->translate('en')?->name ?? $m->id }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-800">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Exercício</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider hidden sm:table-cell">Equipamento</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider hidden md:table-cell">Músculo</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($exercises as $exercise)
                        <tr class="hover:bg-zinc-800/30 transition">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    @if($exercise->thumbnail_path)
                                        <img src="{{ $exercise->thumbnail_url }}"
                                             class="w-10 h-10 rounded-lg object-cover shrink-0 bg-zinc-800">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-zinc-800 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-white">{{ $exercise->translate('en')?->name ?? '—' }}</p>
                                        <p class="text-xs text-zinc-500">{{ $exercise->translate('pt')?->name ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-zinc-400 hidden sm:table-cell">
                                {{ $exercise->equipment?->translate('en')?->name ?? '—' }}
                            </td>
                            <td class="px-5 py-3 text-zinc-400 hidden md:table-cell">
                                {{ $exercise->primaryMuscle?->translate('en')?->name ?? '—' }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="px-2 py-0.5 rounded-full text-xs
                                        {{ $exercise->is_custom ? 'bg-amber-600/20 text-amber-400' : 'bg-zinc-800 text-zinc-500' }}">
                                        {{ $exercise->is_custom ? 'custom' : 'global' }}
                                    </span>
                                    @if($exercise->exercise_type !== 'strength')
                                        <span class="px-2 py-0.5 rounded-full text-xs
                                            {{ $exercise->exercise_type === 'cardio' ? 'bg-blue-600/20 text-blue-400' : 'bg-green-600/20 text-green-400' }}">
                                            {{ $exercise->exercise_type }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openEdit({{ $exercise->id }})"
                                        class="text-xs text-violet-400 hover:text-violet-300 transition px-3 py-1.5 rounded-lg hover:bg-violet-500/10">
                                        Editar
                                    </button>
                                    <button wire:click="delete({{ $exercise->id }})"
                                        wire:confirm="Tens a certeza que queres eliminar este exercício?"
                                        class="text-xs text-red-400 hover:text-red-300 transition px-3 py-1.5 rounded-lg hover:bg-red-500/10">
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-zinc-500">Nenhum exercício encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($exercises->hasPages())
            <div class="px-5 py-4 border-t border-zinc-800">
                {{ $exercises->links() }}
            </div>
        @endif
    </div>

    {{-- Form modal --}}
    @if($showForm)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-start justify-center z-50 p-4 overflow-y-auto">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl w-full max-w-2xl my-8 overflow-hidden">

                {{-- Modal header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-800">
                    <h2 class="text-base font-semibold text-white">
                        {{ $editingId ? 'Editar Exercício' : 'Novo Exercício' }}
                    </h2>
                    <button wire:click="closeForm" class="text-zinc-400 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Equipment + Muscle --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-1">Equipamento *</label>
                            <select wire:model="equipmentId"
                                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
                                <option value="">Selecionar...</option>
                                @foreach($equipmentList as $eq)
                                    <option value="{{ $eq->id }}">{{ $eq->translate('en')?->name ?? $eq->id }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('equipmentId')" class="mt-1" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-1">Músculo Principal *</label>
                            <select wire:model="muscleId"
                                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
                                <option value="">Selecionar...</option>
                                @foreach($muscleList as $m)
                                    <option value="{{ $m->id }}">{{ $m->translate('en')?->name ?? $m->id }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('muscleId')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Tipo de exercício --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Tipo de exercício</label>
                        <select wire:model="exerciseType"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
                            <option value="strength">Força (peso + reps)</option>
                            <option value="cardio">Cardio (duração + distância)</option>
                            <option value="bodyweight">Peso corporal (só reps)</option>
                        </select>
                    </div>

                    {{-- Custom toggle --}}
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="isCustom" class="w-4 h-4 rounded accent-violet-500">
                        <span class="text-sm text-zinc-300">Exercício personalizado (não global)</span>
                    </label>

                    {{-- Translations --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Traduções</label>

                        {{-- Locale tabs --}}
                        <div class="flex border-b border-zinc-800 mb-4">
                            @foreach(['en' => '🇬🇧 EN', 'pt' => '🇵🇹 PT', 'es' => '🇪🇸 ES'] as $locale => $label)
                                <button type="button" wire:click="$set('activeLocale', '{{ $locale }}')"
                                    class="px-4 py-2 text-sm transition border-b-2 -mb-px
                                        {{ $activeLocale === $locale
                                            ? 'border-violet-500 text-violet-400 font-medium'
                                            : 'border-transparent text-zinc-500 hover:text-zinc-300' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>

                        @foreach(['en', 'pt', 'es'] as $locale)
                            <div class="{{ $activeLocale === $locale ? '' : 'hidden' }} space-y-3">
                                <div>
                                    <label class="block text-xs text-zinc-500 mb-1">
                                        Nome {{ $locale === 'en' ? '*' : '(opcional)' }}
                                    </label>
                                    <input type="text"
                                        wire:model="translations.{{ $locale }}.name"
                                        class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
                                    <x-input-error :messages="$errors->get('translations.' . $locale . '.name')" class="mt-1" />
                                </div>
                                <div>
                                    <label class="block text-xs text-zinc-500 mb-1">Descrição (opcional)</label>
                                    <textarea wire:model="translations.{{ $locale }}.description" rows="3"
                                        class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition resize-none"></textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Thumbnail --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Thumbnail</label>
                        @if($keepThumbnail && !$thumbnail)
                            <div class="mb-2 flex items-center gap-3">
                                @php
                                    $previewUrl = str_starts_with($keepThumbnail, 'images/') ? asset($keepThumbnail) : asset('storage/' . $keepThumbnail);
                                @endphp
                                <img src="{{ $previewUrl }}" class="w-16 h-16 rounded-xl object-cover">
                                <button type="button" wire:click="$set('keepThumbnail', '')"
                                    class="text-xs text-red-400 hover:text-red-300 transition">Remover</button>
                            </div>
                        @endif
                        @if($thumbnail)
                            <div class="mb-2">
                                <img src="{{ $thumbnail->temporaryUrl() }}" class="w-16 h-16 rounded-xl object-cover">
                            </div>
                        @endif
                        <input type="file" wire:model="thumbnail" accept="image/*"
                            class="text-sm text-zinc-400 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700">
                        <x-input-error :messages="$errors->get('thumbnail')" class="mt-1" />
                    </div>

                    {{-- Video --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Vídeo (mp4/webm)</label>
                        @if($keepVideo && !$video)
                            <div class="mb-2 flex items-center gap-3">
                                <span class="text-xs text-zinc-400">{{ basename($keepVideo) }}</span>
                                <button type="button" wire:click="$set('keepVideo', '')"
                                    class="text-xs text-red-400 hover:text-red-300 transition">Remover</button>
                            </div>
                        @endif
                        <input type="file" wire:model="video" accept="video/*"
                            class="text-sm text-zinc-400 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700">
                        <x-input-error :messages="$errors->get('video')" class="mt-1" />
                    </div>

                </div>

                {{-- Modal footer --}}
                <div class="flex gap-3 px-6 py-4 border-t border-zinc-800">
                    <button wire:click="closeForm"
                        class="flex-1 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-sm transition">
                        Cancelar
                    </button>
                    <button wire:click="save" wire:loading.attr="disabled"
                        class="flex-1 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-semibold transition disabled:opacity-60">
                        <span wire:loading.remove wire:target="save">Guardar</span>
                        <span wire:loading wire:target="save">A guardar...</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
