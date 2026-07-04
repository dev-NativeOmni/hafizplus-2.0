<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-zinc-200 leading-tight">
                Rapor Digital Terpadu
            </h2>
            <p class="text-sm text-gray-500">
                Pilih santri untuk melihat dan mengelola rapor digital terpadu (Tahfizh, Adab & Tanse).
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter & Search -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm rounded-2xl transition-colors duration-200">
                <form method="GET" action="{{ route('digital-reports.index') }}" class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[240px]">
                        <label for="search" class="block text-xs font-semibold uppercase text-zinc-400 dark:text-zinc-500 mb-2">Cari Santri</label>
                        <input
                            type="text"
                            name="search"
                            id="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama santri..."
                            class="block w-full rounded-xl border-gray-300 dark:border-zinc-700 dark:bg-[#09090b]/40 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        />
                    </div>

                    <div class="w-full sm:w-48">
                        <label for="class_room_id" class="block text-xs font-semibold uppercase text-zinc-400 dark:text-zinc-500 mb-2">Kelas</label>
                        <select name="class_room_id" id="class_room_id" class="block w-full rounded-xl border-gray-300 dark:border-zinc-700 dark:bg-[#09090b]/40 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Semua Kelas</option>
                            @foreach ($classRooms as $classRoom)
                                <option value="{{ $classRoom->id }}" {{ request('class_room_id') == $classRoom->id ? 'selected' : '' }}>
                                    {{ $classRoom->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors min-h-[42px]">
                            Filter
                        </button>
                        @if (request()->anyFilled(['search', 'class_room_id']))
                            <a href="{{ route('digital-reports.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 dark:border-zinc-700 rounded-xl text-sm font-semibold text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors min-h-[42px]">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- List Table -->
            <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 shadow-sm rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-800">
                        <thead class="bg-gray-50 dark:bg-zinc-900/50">
                            <tr class="text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                <th class="px-6 py-4">Nama Santri</th>
                                <th class="px-6 py-4">Nomor Santri (NIS)</th>
                                <th class="px-6 py-4">Kelas</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-150 dark:divide-zinc-850">
                            @forelse ($students as $student)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition duration-150">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $student->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-zinc-400">
                                        {{ $student->student_number ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-350">
                                            {{ $student->classRoom?->name ?: 'Tanpa Kelas' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <a 
                                            href="{{ route('digital-reports.show', $student) }}" 
                                            class="inline-flex items-center gap-1.5 px-4 py-2 border border-indigo-600 dark:border-indigo-500 rounded-xl text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-transparent hover:bg-indigo-50 dark:hover:bg-indigo-950/20 transition duration-150"
                                        >
                                            📄 Lihat Rapor Digital
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-zinc-500">
                                        Tidak ada data santri ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($students->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-800">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
