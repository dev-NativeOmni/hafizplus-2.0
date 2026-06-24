@php
    $user = auth()->user();

    $hasRole = function (string $role) use ($user): bool {
        if (! $user) {
            return false;
        }

        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }

        return ($user->role?->name ?? null) === $role;
    };

    $isSuperAdmin = $hasRole('super_admin');
    $isAdminUser = $hasRole('admin');
    $isTeacher = $hasRole('teacher');
    $isParent = $hasRole('parent');
    $isStudent = $hasRole('student');

    $isAdmin = $isSuperAdmin || $isAdminUser;
    $canManageRecords = $isSuperAdmin || $isAdminUser || $isTeacher;
    $canViewProgress = $isSuperAdmin || $isAdminUser || $isTeacher || $isParent || $isStudent;
    $canViewReports = $isSuperAdmin || $isAdminUser || $isTeacher;
    $canViewAudit = $isSuperAdmin || $isAdminUser;

    $hasRoute = fn (string $name): bool => \Illuminate\Support\Facades\Route::has($name);

    $unreadNotificationCount = 0;

    if ($user && method_exists($user, 'unreadSystemNotifications')) {
        $unreadNotificationCount = $user->unreadSystemNotifications()->count();
    }

    // Variabel aktif untuk kelompok menu dropdown
    $dataMasterActive = request()->routeIs('programs.*') || request()->routeIs('class-rooms.*') || request()->routeIs('teachers.*') || request()->routeIs('parents.*') || request()->routeIs('students.*');
    $setoranActive = request()->routeIs('quick-inputs.*') || request()->routeIs('hafalan-records.*') || request()->routeIs('murajaah-records.*') || request()->routeIs('hafalan-targets.*');
    $laporanActive = request()->routeIs('progress.*') || request()->routeIs('reports.*');
    $systemActive = request()->routeIs('system-notifications.*') || request()->routeIs('audit-logs.*') || request()->routeIs('dev.api-tester');
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="font-bold text-xl text-gray-800">
                        HafizPlus
                    </a>
                </div>

                <div class="hidden space-x-4 lg:space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('*.dashboard')">
                        Dashboard
                    </x-nav-link>

                    {{-- Dropdown Data Master --}}
                    @if ($isAdmin)
                        <div class="inline-flex items-center">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center h-16 px-1 pt-1 border-b-2 {{ $dataMasterActive ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                        <span>Data Master</span>
                                        <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    @if ($hasRoute('programs.index'))
                                        <x-dropdown-link :href="route('programs.index')">
                                            Program
                                        </x-dropdown-link>
                                    @endif
                                    @if ($hasRoute('class-rooms.index'))
                                        <x-dropdown-link :href="route('class-rooms.index')">
                                            Kelas
                                        </x-dropdown-link>
                                    @endif
                                    @if ($hasRoute('teachers.index'))
                                        <x-dropdown-link :href="route('teachers.index')">
                                            Guru
                                        </x-dropdown-link>
                                    @endif
                                    @if ($hasRoute('parents.index'))
                                        <x-dropdown-link :href="route('parents.index')">
                                            Orangtua
                                        </x-dropdown-link>
                                    @endif
                                    @if ($hasRoute('students.index'))
                                        <x-dropdown-link :href="route('students.index')">
                                            Santri
                                        </x-dropdown-link>
                                    @endif
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    {{-- Dropdown Setoran & Target --}}
                    @if ($canManageRecords)
                        <div class="inline-flex items-center">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center h-16 px-1 pt-1 border-b-2 {{ $setoranActive ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                        <span>Setoran & Target</span>
                                        <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    @if ($hasRoute('quick-inputs.index'))
                                        <x-dropdown-link :href="route('quick-inputs.index')">
                                            Input Cepat
                                        </x-dropdown-link>
                                    @endif
                                    @if ($hasRoute('hafalan-records.index'))
                                        <x-dropdown-link :href="route('hafalan-records.index')">
                                            Hafalan
                                        </x-dropdown-link>
                                    @endif
                                    @if ($hasRoute('murajaah-records.index'))
                                        <x-dropdown-link :href="route('murajaah-records.index')">
                                            Murajaah
                                        </x-dropdown-link>
                                    @endif
                                    @if ($hasRoute('hafalan-targets.index'))
                                        <x-dropdown-link :href="route('hafalan-targets.index')">
                                            Target
                                        </x-dropdown-link>
                                    @endif
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    {{-- Dropdown Progress & Laporan --}}
                    @if (($canViewProgress && $hasRoute('progress.index')) || ($canViewReports && $hasRoute('reports.index')))
                        <div class="inline-flex items-center">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center h-16 px-1 pt-1 border-b-2 {{ $laporanActive ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                        <span>Progres & Laporan</span>
                                        <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    @if ($canViewProgress && $hasRoute('progress.index'))
                                        <x-dropdown-link :href="route('progress.index')">
                                            Progress
                                        </x-dropdown-link>
                                    @endif
                                    @if ($canViewReports && $hasRoute('reports.index'))
                                        <x-dropdown-link :href="route('reports.index')">
                                            Laporan
                                        </x-dropdown-link>
                                    @endif
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    {{-- Dropdown Sistem & Log --}}
                    @if ($hasRoute('system-notifications.index') || ($canViewAudit && $hasRoute('audit-logs.index')) || ($isSuperAdmin && app()->environment('local') && $hasRoute('dev.api-tester')))
                        <div class="inline-flex items-center">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center h-16 px-1 pt-1 border-b-2 {{ $systemActive ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                        <span>Sistem & Log</span>
                                        @if ($unreadNotificationCount > 0)
                                            <span class="inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white ms-1">
                                                {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                                            </span>
                                        @endif
                                        <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    @if ($hasRoute('system-notifications.index'))
                                        <x-dropdown-link :href="route('system-notifications.index')">
                                            Notifikasi
                                        </x-dropdown-link>
                                    @endif
                                    @if ($canViewAudit && $hasRoute('audit-logs.index'))
                                        <x-dropdown-link :href="route('audit-logs.index')">
                                            Audit Log
                                        </x-dropdown-link>
                                    @endif
                                    @if ($isSuperAdmin && app()->environment('local') && $hasRoute('dev.api-tester'))
                                        <x-dropdown-link :href="route('dev.api-tester')">
                                            Dev API Tester
                                        </x-dropdown-link>
                                    @endif
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-800 focus:outline-none transition ease-in-out duration-150">
                            <div class="text-left">
                                <div>
                                    {{ $user?->name }}
                                </div>

                                <div class="text-xs text-gray-400">
                                    {{ $user?->role?->display_name ?? $user?->role?->name ?? '-' }}
                                </div>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if ($hasRoute('profile.edit'))
                            <x-dropdown-link :href="route('profile.edit')">
                                Profil
                            </x-dropdown-link>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                             onclick="event.preventDefault(); this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-700 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': ! open }"
                              class="inline-flex"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />

                        <path :class="{ 'hidden': ! open, 'inline-flex': open }"
                              class="hidden"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('*.dashboard')">
                Dashboard
            </x-responsive-nav-link>

            @if ($isAdmin)
                @if ($hasRoute('programs.index'))
                    <x-responsive-nav-link :href="route('programs.index')" :active="request()->routeIs('programs.*')">
                        Program
                    </x-responsive-nav-link>
                @endif

                @if ($hasRoute('class-rooms.index'))
                    <x-responsive-nav-link :href="route('class-rooms.index')" :active="request()->routeIs('class-rooms.*')">
                        Kelas
                    </x-responsive-nav-link>
                @endif

                @if ($hasRoute('teachers.index'))
                    <x-responsive-nav-link :href="route('teachers.index')" :active="request()->routeIs('teachers.*')">
                        Guru
                    </x-responsive-nav-link>
                @endif

                @if ($hasRoute('parents.index'))
                    <x-responsive-nav-link :href="route('parents.index')" :active="request()->routeIs('parents.*')">
                        Orangtua
                    </x-responsive-nav-link>
                @endif

                @if ($hasRoute('students.index'))
                    <x-responsive-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')">
                        Santri
                    </x-responsive-nav-link>
                @endif
            @endif

            @if ($canManageRecords)
                @if ($hasRoute('quick-inputs.index'))
                    <x-responsive-nav-link :href="route('quick-inputs.index')" :active="request()->routeIs('quick-inputs.*')">
                        Input Cepat
                    </x-responsive-nav-link>
                @endif

                @if ($hasRoute('hafalan-records.index'))
                    <x-responsive-nav-link :href="route('hafalan-records.index')" :active="request()->routeIs('hafalan-records.*')">
                        Hafalan
                    </x-responsive-nav-link>
                @endif

                @if ($hasRoute('murajaah-records.index'))
                    <x-responsive-nav-link :href="route('murajaah-records.index')" :active="request()->routeIs('murajaah-records.*')">
                        Murajaah
                    </x-responsive-nav-link>
                @endif

                @if ($hasRoute('hafalan-targets.index'))
                    <x-responsive-nav-link :href="route('hafalan-targets.index')" :active="request()->routeIs('hafalan-targets.*')">
                        Target
                    </x-responsive-nav-link>
                @endif
            @endif

            @if ($canViewProgress && $hasRoute('progress.index'))
                <x-responsive-nav-link :href="route('progress.index')" :active="request()->routeIs('progress.*')">
                    Progress
                </x-responsive-nav-link>
            @endif

            @if ($canViewReports && $hasRoute('reports.index'))
                <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                    Laporan
                </x-responsive-nav-link>
            @endif

            @if ($hasRoute('system-notifications.index'))
                <x-responsive-nav-link :href="route('system-notifications.index')" :active="request()->routeIs('system-notifications.*')">
                    <span class="inline-flex items-center gap-2">
                        Notifikasi

                        @if ($unreadNotificationCount > 0)
                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-bold text-white">
                                {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                            </span>
                        @endif
                    </span>
                </x-responsive-nav-link>
            @endif

            @if ($canViewAudit && $hasRoute('audit-logs.index'))
                <x-responsive-nav-link :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')">
                    Audit
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">
                    {{ $user?->name }}
                </div>

                <div class="font-medium text-sm text-gray-500">
                    {{ $user?->email }}
                </div>

                <div class="mt-1 text-xs text-gray-400">
                    {{ $user?->role?->display_name ?? $user?->role?->name ?? '-' }}
                </div>
            </div>

            <div class="mt-3 space-y-1">
                @if ($hasRoute('profile.edit'))
                    <x-responsive-nav-link :href="route('profile.edit')">
                        Profil
                    </x-responsive-nav-link>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                                           onclick="event.preventDefault(); this.closest('form').submit();">
                        Keluar
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>