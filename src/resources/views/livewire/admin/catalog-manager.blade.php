<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Catálogo</h1>
            <p class="text-sm text-zinc-500 mt-1">Gere equipamento e grupos musculares</p>
        </div>
        <button wire:click="openCreate('{{ $tab }}')"
            class="px-4 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-semibold text-white transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Novo
        </button>
    </div>

    {{-- Tabs --}}
    <div class="flex border-b border-zinc-800">
        <button wire:click="setTab('equipment')"
            class="px-5 py-2.5 text-sm font-medium transition border-b-2 -mb-px
                {{ $tab === 'equipment' ? 'border-violet-500 text-violet-400' : 'border-transparent text-zinc-500 hover:text-zinc-300' }}">
            Equipamento ({{ $equipment->count() }})
        </button>
        <button wire:click="setTab('muscles')"
            class="px-5 py-2.5 text-sm font-medium transition border-b-2 -mb-px
                {{ $tab === 'muscles' ? 'border-violet-500 text-violet-400' : 'border-transparent text-zinc-500 hover:text-zinc-300' }}">
            Músculos ({{ $muscles->count() }})
        </button>
    </div>

    {{-- Equipment tab --}}
    @if($tab === 'equipment')
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-800">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Nome (EN)</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider hidden sm:table-cell">PT</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider hidden sm:table-cell">ES</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider hidden md:table-cell">Exercícios</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($equipment as $eq)
                        <tr class="hover:bg-zinc-800/30 transition">
                            <td class="px-5 py-3 font-medium text-white">{{ $eq->translate('en')?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-zinc-400 hidden sm:table-cell">{{ $eq->translate('pt')?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-zinc-400 hidden sm:table-cell">{{ $eq->translate('es')?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-zinc-500 hidden md:table-cell">{{ $eq->exercises()->count() }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openEdit('equipment', {{ $eq->id }})"
                                        class="text-xs text-violet-400 hover:text-violet-300 px-3 py-1.5 rounded-lg hover:bg-violet-500/10 transition">
                                        Editar
                                    </button>
                                    @if($eq->exercises()->count() === 0)
                                        <button wire:click="delete('equipment', {{ $eq->id }})"
                                            wire:confirm="Eliminar este equipamento?"
                                            class="text-xs text-red-400 hover:text-red-300 px-3 py-1.5 rounded-lg hover:bg-red-500/10 transition">
                                            Eliminar
                                        </button>
                                    @else
                                        <span class="text-xs text-zinc-700 px-3 py-1.5">Em uso</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-zinc-500">Nenhum equipamento.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    {{-- Muscles tab --}}
    @if($tab === 'muscles')
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-800">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Nome (EN)</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider hidden sm:table-cell">PT</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider hidden sm:table-cell">ES</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider hidden md:table-cell">Exercícios</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($muscles as $muscle)
                        <tr class="hover:bg-zinc-800/30 transition">
                            <td class="px-5 py-3 font-medium text-white">{{ $muscle->translate('en')?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-zinc-400 hidden sm:table-cell">{{ $muscle->translate('pt')?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-zinc-400 hidden sm:table-cell">{{ $muscle->translate('es')?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-zinc-500 hidden md:table-cell">{{ $muscle->exercises()->count() }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openEdit('muscles', {{ $muscle->id }})"
                                        class="text-xs text-violet-400 hover:text-violet-300 px-3 py-1.5 rounded-lg hover:bg-violet-500/10 transition">
                                        Editar
                                    </button>
                                    @if($muscle->exercises()->count() === 0)
                                        <button wire:click="delete('muscles', {{ $muscle->id }})"
                                            wire:confirm="Eliminar este músculo?"
                                            class="text-xs text-red-400 hover:text-red-300 px-3 py-1.5 rounded-lg hover:bg-red-500/10 transition">
                                            Eliminar
                                        </button>
                                    @else
                                        <span class="text-xs text-zinc-700 px-3 py-1.5">Em uso</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-zinc-500">Nenhum músculo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    {{-- Form modal --}}
    @if($showForm)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl w-full max-w-md overflow-hidden">

                <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-800">
                    <h2 class="text-base font-semibold text-white">
                        {{ $editingId ? 'Editar' : 'Novo' }}
                        {{ $formType === 'equipment' ? 'Equipamento' : 'Músculo' }}
                    </h2>
                    <button wire:click="closeForm" class="text-zinc-400 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    {{-- Locale tabs --}}
                    <div class="flex border-b border-zinc-800 mb-2">
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
                        <div class="{{ $activeLocale === $locale ? '' : 'hidden' }}">
                            <label class="block text-xs text-zinc-500 mb-1">
                                Nome {{ $locale === 'en' ? '*' : '(opcional)' }}
                            </label>
                            <input type="text"
                                wire:model="translations.{{ $locale }}.name"
                                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
                            <x-input-error :messages="$errors->get('translations.' . $locale . '.name')" class="mt-1" />
                        </div>
                    @endforeach
                </div>

                <div class="flex gap-3 px-6 py-4 border-t border-zinc-800">
                    <button wire:click="closeForm"
                        class="flex-1 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-sm transition">
                        Cancelar
                    </button>
                    <button wire:click="save" wire:loading.attr="disabled" wire:target="save"
                        class="flex-1 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-semibold transition disabled:opacity-60">
                        <span wire:loading.remove wire:target="save">Guardar</span>
                        <span wire:loading wire:target="save">A guardar...</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
