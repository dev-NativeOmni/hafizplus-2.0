<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    Detail Progress — {{ $student->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $student->student_number ?? '-' }}
                    @if ($student->classRoom)
                        · {{ $student->classRoom->name }}
                    @endif
                    @if ($student->classRoom?->program)
                        · {{ $student->classRoom->program->name }}
                    @endif
                </p>
            </div>

            <a href="{{ route('progress.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                Kembali ke Progress
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Progress Hafalan</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ number_format((float) ($progress['progress_percent'] ?? 0), 2) }}%
                    </p>

                    <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-gray-100">
                        <div class="h-2.5 rounded-full bg-emerald-600"
                             style="width: {{ min(100, max(0, (float) ($progress['progress_percent'] ?? 0))) }}%"></div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Ayat Hafal</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ number_format($progress['memorized_ayahs'] ?? 0) }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        dari {{ number_format($progress['total_quran_ayahs'] ?? 0) }} ayat
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Total Setoran</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ number_format($progress['total_hafalan_records'] ?? 0) }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        Nilai rata-rata: {{ number_format((float) ($progress['average_hafalan_score'] ?? 0), 2) }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Target Terlambat</p>
                    <p class="mt-2 text-3xl font-bold text-red-600">
                        {{ number_format($progress['overdue_targets'] ?? 0) }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        Target aktif: {{ number_format($progress['active_targets'] ?? 0) }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                <div class="xl:col-span-2 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-3 border-b border-gray-100 pb-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">
                                Progress Per Surah
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Hanya menampilkan surah yang sudah punya setoran lulus.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Surah</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Ayat Hafal</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Progress</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Rentang</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($surahProgressRows as $row)
                                    <tr>
                                        <td class="px-4 py-3 align-top">
                                            <div class="font-semibold text-gray-900">
                                                {{ $row['surah']->number }}. {{ $row['surah']->name_latin }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $row['surah']->name_ar ?? '-' }}
                                            </div>
                                        </td>

                                        <td class="px-4 py-3 align-top">
                                            {{ number_format($row['memorized_ayahs']) }}
                                            /
                                            {{ number_format($row['total_ayahs']) }}
                                        </td>

                                        <td class="px-4 py-3 align-top">
                                            <div class="mb-1 text-xs font-semibold text-gray-700">
                                                {{ number_format((float) $row['progress_percent'], 2) }}%
                                            </div>
                                            <div class="h-2 w-44 overflow-hidden rounded-full bg-gray-100">
                                                <div class="h-2 rounded-full bg-emerald-600"
                                                     style="width: {{ min(100, max(0, (float) $row['progress_percent'])) }}%"></div>
                                            </div>
                                        </td>

                                        <td class="px-4 py-3 align-top text-gray-700">
                                            {{ $row['ranges'] ?: '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                            Belum ada hafalan lulus untuk santri ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-semibold text-gray-900">
                        Timeline Terbaru
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Gabungan hafalan, murajaah, dan target.
                    </p>

                    <div class="mt-5 space-y-4">
                        @forelse ($timelineRows as $item)
                            @php
                                $badgeClass = match ($item['type']) {
                                    'hafalan' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'murajaah' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'target' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-gray-50 text-gray-700 border-gray-200',
                                };

                                $statusLabel = match ($item['status']) {
                                    'passed' => 'Lulus',
                                    'repeat' => 'Ulang',
                                    'needs_improvement' => 'Perlu Perbaikan',
                                    'active' => 'Aktif',
                                    'planned' => 'Direncanakan',
                                    'in_progress' => 'Berjalan',
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Dibatalkan',
                                    default => $item['status'] ?? '-',
                                };
                            @endphp

                            <div class="rounded-xl border border-gray-200 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
                                        {{ $item['label'] }}
                                    </span>

                                    <span class="text-xs text-gray-500">
                                        {{ $item['date'] ? \Carbon\Carbon::parse($item['date'])->format('d M Y') : '-' }}
                                    </span>
                                </div>

                                <div class="mt-3 font-semibold text-gray-900">
                                    {{ $item['title'] }} · Ayat {{ $item['range'] }}
                                </div>

                                <div class="mt-1 text-sm text-gray-600">
                                    Status: {{ $statusLabel }}
                                    @if ($item['score'] !== null)
                                        · Nilai: {{ number_format((float) $item['score'], 2) }}
                                    @endif
                                </div>

                                @if ($item['teacher'])
                                    <div class="mt-1 text-xs text-gray-500">
                                        Guru: {{ $item['teacher'] }}
                                    </div>
                                @endif

                                @if ($item['notes'])
                                    <div class="mt-2 rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-600">
                                        {{ $item['notes'] }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                                Belum ada timeline.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-semibold text-gray-900">
                        Riwayat Hafalan
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Surah</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Ayat</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Nilai</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Guru</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($hafalanRecords as $record)
                                <tr>
                                    <td class="px-5 py-3">
                                        {{ $record->submitted_at ? \Carbon\Carbon::parse($record->submitted_at)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        {{ $record->surah?->name_latin ?? '-' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        {{ $record->ayah_start }} - {{ $record->ayah_end }}
                                    </td>
                                    <td class="px-5 py-3">
                                        {{ match ($record->status) {
                                            'passed' => 'Lulus',
                                            'repeat' => 'Ulang',
                                            'needs_improvement' => 'Perlu Perbaikan',
                                            default => $record->status,
                                        } }}
                                    </td>
                                    <td class="px-5 py-3">
                                        {{ $record->score !== null ? number_format((float) $record->score, 2) : '-' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        {{ $record->teacher?->user?->name ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                        Belum ada riwayat hafalan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $hafalanRecords->links() }}
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-semibold text-gray-900">
                        Riwayat Murajaah
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Surah</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Ayat</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Nilai</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Guru</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($murajaahRecords as $record)
                                <tr>
                                    <td class="px-5 py-3">
                                        {{ $record->reviewed_at ? \Carbon\Carbon::parse($record->reviewed_at)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        {{ $record->surah?->name_latin ?? '-' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        {{ $record->ayah_start }} - {{ $record->ayah_end }}
                                    </td>
                                    <td class="px-5 py-3">
                                        {{ match ($record->status) {
                                            'passed' => 'Lulus',
                                            'repeat' => 'Ulang',
                                            'needs_improvement' => 'Perlu Perbaikan',
                                            default => $record->status,
                                        } }}
                                    </td>
                                    <td class="px-5 py-3">
                                        @php
                                            $murajaahScore = $record->overall_score ?? $record->score ?? null;
                                        @endphp

                                        {{ $murajaahScore !== null ? number_format((float) $murajaahScore, 2) : '-' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        {{ $record->teacher?->user?->name ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                        Belum ada riwayat murajaah.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $murajaahRecords->links() }}
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-semibold text-gray-900">
                        Target Hafalan
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Target Date</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Surah</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Ayat</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Selesai</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Guru</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($targets as $target)
                                <tr>
                                    <td class="px-5 py-3">
                                        {{ $target->target_date ? \Carbon\Carbon::parse($target->target_date)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        {{ $target->surah?->name_latin ?? '-' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        {{ $target->ayah_start }} - {{ $target->ayah_end }}
                                    </td>
                                    <td class="px-5 py-3">
                                        {{ match ($target->status) {
                                            'active' => 'Aktif',
                                            'planned' => 'Direncanakan',
                                            'in_progress' => 'Berjalan',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                            default => $target->status,
                                        } }}
                                    </td>
                                    <td class="px-5 py-3">
                                        {{ $target->completed_at ? \Carbon\Carbon::parse($target->completed_at)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        {{ $target->teacher?->user?->name ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                        Belum ada target hafalan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $targets->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>