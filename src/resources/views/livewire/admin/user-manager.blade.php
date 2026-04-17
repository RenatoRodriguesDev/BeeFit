<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-white">Utilizadores</h1>
        <p class="text-sm text-zinc-500 mt-1">Gere roles, planos e subscrições</p>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="Pesquisar por nome, email ou username..."
            class="flex-1 bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">

        <select wire:model.live="filterRole"
            class="bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
            <option value="">Todos os roles</option>
            <option value="admin">Admin</option>
            <option value="trainer">Trainer</option>
            <option value="user">User</option>
        </select>

        <select wire:model.live="filterPlan"
            class="bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
            <option value="">Todos os planos</option>
            <option value="premium">Premium</option>
            <option value="trainer">Trainer</option>
            <option value="free">Free</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-800">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">
                            Utilizador</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">
                            Role</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">
                            Plano</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">
                            Subscrição</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">
                            Registado</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($users as $user)
                                    <tr class="hover:bg-zinc-800/30 transition">
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center text-xs font-semibold text-zinc-400 shrink-0">
                                                    {{ $user->initials() }}
                                                </div>
                                                <div>
                                                    <p class="font-medium text-white">{{ $user->name }}</p>
                                                    <p class="text-xs text-zinc-500">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                                                    {{ $user->role === 'admin' ? 'bg-violet-600/20 text-violet-400' :
                        ($user->role === 'trainer' ? 'bg-blue-600/20 text-blue-400' : 'bg-zinc-800 text-zinc-400') }}">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                                                    {{ $user->plan === 'premium' ? 'bg-emerald-600/20 text-emerald-400' :
                        ($user->plan === 'trainer' ? 'bg-blue-600/20 text-blue-400' : 'bg-zinc-800 text-zinc-500') }}">
                                                {{ $user->plan ?? 'free' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3">
                                            @php $status = $user->subscription_status ?? 'none'; @endphp
                                            <span
                                                class="px-2 py-0.5 rounded-full text-xs
                                                                    {{ $status === 'active' ? 'bg-emerald-600/20 text-emerald-400' :
                        ($status === 'trialing' ? 'bg-blue-600/20 text-blue-400' :
                            ($status === 'past_due' ? 'bg-amber-600/20 text-amber-400' :
                                ($status === 'canceled' ? 'bg-red-600/20 text-red-400' : 'bg-zinc-800 text-zinc-500'))) }}">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-zinc-500 text-xs">
                                            {{ $user->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            <button wire:click="editUser({{ $user->id }})"
                                                class="text-xs text-violet-400 hover:text-violet-300 transition px-3 py-1.5 rounded-lg hover:bg-violet-500/10">
                                                Editar
                                            </button>
                                            <button wire:click="delete({{ $user->id }})" wire:confirm="Eliminar este utilizador?"
                                                class="text-xs text-red-400 hover:text-red-300 px-3 py-1.5 rounded-lg hover:bg-red-500/10 transition">
                                                Eliminar
                                            </button>
                                        </td>
                                    </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-zinc-500">Nenhum utilizador encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-zinc-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Edit modal --}}
    @if($editingUserId)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 w-full max-w-sm space-y-5">

                <h2 class="text-base font-semibold text-white">Editar Utilizador</h2>

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Role</label>
                    <select wire:model.live="editRole"
                        class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
                        <option value="user">user</option>
                        <option value="trainer">trainer</option>
                        <option value="admin">admin</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Plano</label>
                    <select wire:model.live="editPlan"
                        class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
                        <option value="free">free</option>
                        <option value="premium">premium</option>
                        <option value="trainer">trainer</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Estado da Subscrição</label>
                    <select wire:model.live="editSubscriptionStatus"
                        class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
                        <option value="none">none</option>
                        <option value="active">active</option>
                        <option value="trialing">trialing</option>
                        <option value="past_due">past_due</option>
                        <option value="canceled">canceled</option>
                        <option value="incomplete">incomplete</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-1">
                    <button wire:click="cancelEdit"
                        class="flex-1 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-sm transition">
                        Cancelar
                    </button>
                    <button wire:click="saveUser"
                        class="flex-1 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-semibold transition">
                        Guardar
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>