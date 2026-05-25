<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    Progress Santri
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Rekap progres hafalan berdasarkan ayat lulus, murajaah, target aktif, dan target terlambat.
                </p>
            </div>

            @if (Route::has('reports.index'))
                <a href="{{ route('reports.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    Buka Laporan
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('progress.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                    <div>
                        <label for="q" class="mb-1 block text-sm font-semibold text-gray-700">
                            Cari
                        </label>
                        <input id="q"
                               type="text"
                               name="q"
                               value="{{ request('q') }}"
                               placeholder="Nama / nomor santri"
                               class="w-full rounded-lg border-gray-300 text-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label for="student_id" class="mb-1 block text-sm font-semibold text-gray-700">
                            Santri
                        </label>
                        <select id="student_id"
                                name="student_id"
                                class="w-full rounded-lg border-gray-300 text-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Semua Santri</option>
                            @foreach ($filterStudents as $student)
                                <option value="{{ $student->id }}" @selected((string) request('student_id') === (string) $student->id)>
                                    {{ $student->name }}
                                    @if ($student->student_number)
                                        — {{ $student->student_number }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="class_room_id" class="mb-1 block text-sm font-semibold text-gray-700">
                            Kelas
                        </label>
                        <select id="class_room_id"
                                name="class_room_id"
                                class="w-full rounded-lg border-gray-300 text-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Semua Kelas</option>
                            @foreach ($classRooms as $classRoom)
                                <option value="{{ $classRoom->id }}" @selected((string) request('class_room_id') === (string) $classRoom->id)>
                                    {{ $classRoom->name }}
                                    @if ($classRoom->program)
                                        — {{ $classRoom->program->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="sort" class="mb-1 block text-sm font-semibold text-gray-700">
                            Urutkan
                        </label>
                        <select id="sort"
                                name="sort"
                                class="w-full rounded-lg border-gray-300 text-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Progress Tertinggi</option>
                            <option value="low_progress" @selected(request('sort') === 'low_progress')>Progress Terendah</option>
                            <option value="overdue" @selected(request('sort') === 'overdue')>Target Terlambat</option>
                            <option value="name" @selected(request('sort') === 'name')>Nama Santri</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                            Filter
                        </button>

                        <a href="{{ route('progress.index') }}"
                           class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Total Santri</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ number_format($summary['total_students'] ?? 0) }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Total Ayat Hafal</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ number_format($summary['total_memorized_ayahs'] ?? 0) }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Rata-rata Progress</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ number_format((float) ($summary['average_progress_percent'] ?? 0), 2) }}%
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Target Terlambat</p>
                    <p class="mt-2 text-3xl font-bold text-red-600">
                        {{ number_format($summary['total_overdue_targets'] ?? 0) }}
                    </p>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-semibold text-gray-900">
                        Daftar Progress Santri
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Klik detail untuk melihat timeline dan progres per surah.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Santri</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Kelas</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Progress</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Hafalan</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Murajaah</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Target</th>
                                <th class="px-5 py-3 text-right font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($progressRows as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4 align-top">
                                        <div class="font-semibold text-gray-900">
                                            {{ $row['student_name'] }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $row['student_number'] ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        <div class="text-gray-900">
                                            {{ $row['class_room_name'] ?? '-' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $row['program_name'] ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        <div class="mb-1 flex items-center justify-between gap-3">
                                            <span class="font-semibold text-gray-900">
                                                {{ number_format((float) $row['progress_percent'], 2) }}%
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                {{ number_format($row['memorized_ayahs']) }} / {{ number_format($row['total_quran_ayahs']) }} ayat
                                            </span>
                                        </div>

                                        <div class="h-2.5 w-56 overflow-hidden rounded-full bg-gray-100">
                                            <div class="h-2.5 rounded-full bg-emerald-600"
                                                 style="width: {{ min(100, max(0, (float) $row['progress_percent'])) }}%"></div>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        <div class="font-medium text-gray-900">
                                            {{ number_format($row['total_hafalan_records']) }} setoran
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Rata-rata nilai: {{ number_format((float) $row['average_hafalan_score'], 2) }}
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        <div class="font-medium text-gray-900">
                                            {{ number_format($row['total_murajaah_records']) }} murajaah
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Rata-rata nilai: {{ number_format((float) $row['average_murajaah_score'], 2) }}
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        <div class="font-medium text-gray-900">
                                            Aktif: {{ number_format($row['active_targets']) }}
                                        </div>

                                        @if (($row['overdue_targets'] ?? 0) > 0)
                                            <div class="text-xs font-semibold text-red-600">
                                                Terlambat: {{ number_format($row['overdue_targets']) }}
                                            </div>
                                        @else
                                            <div class="text-xs text-gray-500">
                                                Tidak ada overdue
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 text-right align-top">
                                        <a href="{{ route('progress.show', $row['student_id']) }}"
                                           class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-3 py-2 text-xs font-semibold text-white hover:bg-gray-800">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-gray-500">
                                        Belum ada data progress yang sesuai filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>