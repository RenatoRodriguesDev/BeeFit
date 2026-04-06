<div class="space-y-8">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-white">Dashboard</h1>
        <p class="text-sm text-zinc-500 mt-1">Visão geral da plataforma BeeFit</p>
    </div>

    {{-- Stat cards row 1: Users --}}
    <div>
        <h2 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-3">Utilizadores</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5">
                <p class="text-xs text-zinc-500 mb-1">Total</p>
                <p class="text-3xl font-bold text-white">{{ $stats['total_users'] }}</p>
                <p class="text-xs text-zinc-500 mt-1">+{{ $stats['users_today'] }} hoje</p>
            </div>
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5">
                <p class="text-xs text-zinc-500 mb-1">Este mês</p>
                <p class="text-3xl font-bold text-violet-400">{{ $stats['users_this_month'] }}</p>
                <p class="text-xs text-zinc-500 mt-1">novos registos</p>
            </div>
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5">
                <p class="text-xs text-zinc-500 mb-1">Premium ativos</p>
                <p class="text-3xl font-bold text-emerald-400">{{ $stats['premium_users'] }}</p>
                <p class="text-xs text-zinc-500 mt-1">subscrições ativas</p>
            </div>
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5">
                <p class="text-xs text-zinc-500 mb-1">Trainers</p>
                <p class="text-3xl font-bold text-blue-400">{{ $stats['trainer_users'] }}</p>
                <p class="text-xs text-zinc-500 mt-1">+ {{ $stats['admin_users'] }} admin(s)</p>
            </div>
        </div>
    </div>

    {{-- Stat cards row 2: Activity --}}
    <div>
        <h2 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-3">Atividade</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5">
                <p class="text-xs text-zinc-500 mb-1">Treinos concluídos</p>
                <p class="text-3xl font-bold text-white">{{ $stats['total_workouts'] }}</p>
                <p class="text-xs text-zinc-500 mt-1">+{{ $stats['workouts_today'] }} hoje</p>
            </div>
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5">
                <p class="text-xs text-zinc-500 mb-1">Posts</p>
                <p class="text-3xl font-bold text-white">{{ $stats['total_posts'] }}</p>
                <p class="text-xs text-zinc-500 mt-1">+{{ $stats['posts_today'] }} hoje</p>
            </div>
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5">
                <p class="text-xs text-zinc-500 mb-1">Exercícios</p>
                <p class="text-3xl font-bold text-white">{{ $stats['total_exercises'] }}</p>
                <p class="text-xs text-zinc-500 mt-1">{{ $stats['custom_exercises'] }} personalizados</p>
            </div>
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5">
                <p class="text-xs text-zinc-500 mb-1">Ratio premium</p>
                <p class="text-3xl font-bold text-amber-400">
                    {{ $stats['total_users'] > 0 ? round(($stats['premium_users'] / $stats['total_users']) * 100) : 0 }}%
                </p>
                <p class="text-xs text-zinc-500 mt-1">utilizadores pagantes</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Subscription breakdown --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <h2 class="text-sm font-semibold text-white mb-4">Estado das Subscrições</h2>
            <div class="space-y-3">
                @foreach([
                    'active'   => ['label' => 'Ativas',        'color' => 'bg-emerald-500'],
                    'trialing' => ['label' => 'Em trial',       'color' => 'bg-blue-500'],
                    'past_due' => ['label' => 'Em atraso',      'color' => 'bg-amber-500'],
                    'canceled' => ['label' => 'Canceladas',     'color' => 'bg-red-500'],
                    'none'     => ['label' => 'Sem subscrição', 'color' => 'bg-zinc-600'],
                ] as $key => $info)
                    @php $count = $subscriptionBreakdown[$key]; $total = $stats['total_users'] ?: 1; @endphp
                    <div class="flex items-center gap-3">
                        <div class="w-2.5 h-2.5 rounded-full {{ $info['color'] }} shrink-0"></div>
                        <span class="text-sm text-zinc-400 flex-1">{{ $info['label'] }}</span>
                        <span class="text-sm font-semibold text-white">{{ $count }}</span>
                        <span class="text-xs text-zinc-600 w-10 text-right">{{ round(($count / $total) * 100) }}%</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Quick links --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <h2 class="text-sm font-semibold text-white mb-4">Gestão</h2>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-3 rounded-xl bg-zinc-800 hover:bg-zinc-700 transition">
                    <span class="text-xl">👥</span>
                    <span class="text-sm font-medium text-white">Utilizadores</span>
                </a>
                <a href="{{ route('admin.exercises') }}" class="flex items-center gap-3 p-3 rounded-xl bg-zinc-800 hover:bg-zinc-700 transition">
                    <span class="text-xl">🏋️</span>
                    <span class="text-sm font-medium text-white">Exercícios</span>
                </a>
                <a href="{{ route('admin.catalog') }}" class="flex items-center gap-3 p-3 rounded-xl bg-zinc-800 hover:bg-zinc-700 transition">
                    <span class="text-xl">📚</span>
                    <span class="text-sm font-medium text-white">Catálogo</span>
                </a>
                <a href="{{ route('admin.achievements') }}" class="flex items-center gap-3 p-3 rounded-xl bg-zinc-800 hover:bg-zinc-700 transition">
                    <span class="text-xl">🏆</span>
                    <span class="text-sm font-medium text-white">Conquistas</span>
                </a>
            </div>
        </div>

        {{-- Recent users --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-white">Utilizadores Recentes</h2>
                <a href="{{ route('admin.users') }}" class="text-xs text-violet-400 hover:text-violet-300 transition">Ver todos</a>
            </div>
            <div class="space-y-2">
                @foreach($recentUsers as $u)
                    <div class="flex items-center gap-3 py-1.5">
                        <div class="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center text-xs font-semibold text-zinc-400 shrink-0">
                            {{ $u->initials() }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ $u->name }}</p>
                            <p class="text-xs text-zinc-500 truncate">{{ $u->email }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full shrink-0
                            {{ $u->role === 'admin' ? 'bg-violet-600/20 text-violet-400' :
                               ($u->role === 'trainer' ? 'bg-blue-600/20 text-blue-400' :
                               ($u->subscription_status === 'active' ? 'bg-emerald-600/20 text-emerald-400' : 'bg-zinc-800 text-zinc-500')) }}">
                            {{ $u->role === 'admin' ? 'admin' : ($u->role === 'trainer' ? 'trainer' : ($u->subscription_status === 'active' ? 'premium' : 'free')) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
