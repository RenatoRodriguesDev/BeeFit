<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Conquistas</h1>
            <p class="text-sm text-zinc-500 mt-1">Gere os achievements e as suas traduções</p>
        </div>
        <button wire:click="openCreate"
            class="px-4 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-semibold text-white transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Nova Conquista
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-800">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider w-10">Ícone</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Key</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider hidden sm:table-cell">Nome (EN)</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider hidden md:table-cell">Descrição (EN)</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider w-20">XP</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider hidden sm:table-cell">Users</th>
                    <th class="px-5 py-3 w-20"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/60">
                @forelse($achievements as $ach)
                    <tr class="hover:bg-zinc-800/30 transition">
                        <td class="px-5 py-3 text-xl">{{ $ach->icon }}</td>
                        <td class="px-5 py-3 font-mono text-xs text-zinc-300">{{ $ach->key }}</td>
                        <td class="px-5 py-3 font-medium text-white hidden sm:table-cell">{{ $ach->name }}</td>
                        <td class="px-5 py-3 text-zinc-400 hidden md:table-cell max-w-xs truncate">{{ $ach->description }}</td>
                        <td class="px-5 py-3 text-yellow-400 font-bold">+{{ $ach->xp_reward }}</td>
                        <td class="px-5 py-3 text-zinc-400 hidden sm:table-cell">{{ $ach->users()->count() }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2 justify-end">
                                <button wire:click="openEdit({{ $ach->id }})"
                                    class="text-xs text-zinc-400 hover:text-white transition px-2 py-1 rounded-lg hover:bg-zinc-700">
                                    Editar
                                </button>
                                <button wire:click="delete({{ $ach->id }})"
                                    wire:confirm="Tens a certeza? Esta ação não pode ser desfeita."
                                    class="text-xs text-red-500 hover:text-red-400 transition px-2 py-1 rounded-lg hover:bg-zinc-700">
                                    Apagar
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-zinc-500">Nenhuma conquista criada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Slide-over form --}}
    @if($showForm)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40" wire:click="closeForm"></div>
        <div class="fixed right-0 top-0 bottom-0 w-full max-w-md bg-zinc-900 border-l border-zinc-800 z-50 overflow-y-auto p-6 space-y-5">

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold">{{ $editingId ? 'Editar Conquista' : 'Nova Conquista' }}</h2>
                <button wire:click="closeForm" class="text-zinc-500 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Key & Icon & XP --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-1">Key <span class="text-red-400">*</span></label>
                    <input wire:model="key" type="text" placeholder="ex: first_workout"
                        @if($editingId) readonly class="w-full px-3 py-2 rounded-lg bg-zinc-800 border border-zinc-700 text-zinc-400 text-sm font-mono cursor-not-allowed"
                        @else class="w-full px-3 py-2 rounded-lg bg-zinc-800 border border-zinc-700 text-white text-sm font-mono focus:outline-none focus:border-violet-500"
                        @endif>
                    @error('key') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-1">Ícone (emoji) <span class="text-red-400">*</span></label>
                    <input wire:model="icon" type="text" placeholder="🏆" maxlength="10"
                        class="w-full px-3 py-2 rounded-lg bg-zinc-800 border border-zinc-700 text-white text-2xl text-center focus:outline-none focus:border-violet-500">
                    @error('icon') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-400 mb-1">XP Reward <span class="text-red-400">*</span></label>
                <input wire:model="xp_reward" type="number" min="0" max="9999"
                    class="w-full px-3 py-2 rounded-lg bg-zinc-800 border border-zinc-700 text-white text-sm focus:outline-none focus:border-violet-500">
                @error('xp_reward') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Language tabs --}}
            <div>
                <div class="flex border-b border-zinc-800 mb-4">
                    @foreach(['en' => '🇬🇧 EN', 'pt' => '🇵🇹 PT', 'es' => '🇪🇸 ES'] as $loc => $label)
                        <button wire:click="$set('activeLocale', '{{ $loc }}')"
                            class="px-4 py-2 text-sm font-medium transition border-b-2 -mb-px
                                {{ $activeLocale === $loc ? 'border-violet-500 text-violet-400' : 'border-transparent text-zinc-500 hover:text-zinc-300' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                @foreach(['en', 'pt', 'es'] as $loc)
                    <div class="{{ $activeLocale === $loc ? '' : 'hidden' }} space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-400 mb-1">Nome <span class="text-red-400">*</span></label>
                            <input wire:model="translations.{{ $loc }}.name" type="text" placeholder="Nome da conquista"
                                class="w-full px-3 py-2 rounded-lg bg-zinc-800 border border-zinc-700 text-white text-sm focus:outline-none focus:border-violet-500">
                            @error("translations.{$loc}.name") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-400 mb-1">Descrição <span class="text-red-400">*</span></label>
                            <textarea wire:model="translations.{{ $loc }}.description" rows="2" placeholder="Descrição da conquista"
                                class="w-full px-3 py-2 rounded-lg bg-zinc-800 border border-zinc-700 text-white text-sm focus:outline-none focus:border-violet-500 resize-none"></textarea>
                            @error("translations.{$loc}.description") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <button wire:click="save"
                    class="flex-1 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm transition">
                    {{ $editingId ? 'Guardar' : 'Criar' }}
                </button>
                <button wire:click="closeForm"
                    class="px-4 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm transition">
                    Cancelar
                </button>
            </div>
        </div>
    @endif

</div>
