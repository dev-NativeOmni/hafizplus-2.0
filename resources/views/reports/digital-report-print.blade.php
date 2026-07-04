<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor Digital Terpadu - {{ $student->name }}</title>
    <!-- Tailwind CSS v4 via CDN for print layout rendering -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: white !important;
                color: black !important;
            }
            .page-break {
                page-break-before: always;
            }
        }
        body {
            font-family: 'Times New Roman', Times, serif;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 p-4 sm:p-8">

    <!-- Floating Action Button for print (hidden on print) -->
    <div class="max-w-4xl mx-auto mb-6 flex justify-between items-center no-print bg-white p-4 rounded-xl border shadow-sm">
        <span class="text-sm text-gray-500 font-semibold">📄 Preview Cetak Dokumen Rapor Terpadu</span>
        <div class="flex gap-2">
            <button onclick="window.close()" class="px-4 py-2 border rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50">
                Tutup Halaman
            </button>
            <button onclick="window.print()" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold shadow">
                Cetak Sekarang
            </button>
        </div>
    </div>

    <!-- Official Report Card Layout -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 border shadow-sm rounded-none min-h-[297mm]">
        
        <!-- Kop Surat Sekolah -->
        <div class="text-center border-b-4 border-double border-black pb-4 mb-6">
            <h1 class="text-xl font-bold uppercase tracking-wide">SMA ISLAM AL AZHAR 7 SOLO BARU</h1>
            <p class="text-xs text-gray-600 mt-1">Jl. KH. Samanhudi No. 1, Solo Baru, Sukoharjo, Jawa Tengah</p>
            <h2 class="text-lg font-bold uppercase mt-2 tracking-wider text-indigo-900">RAPOR DIGITAL MONITORING SISWA</h2>
            <p class="text-sm text-gray-500 font-semibold mt-0.5">Tahun Ajaran: {{ $academicYear }} · Semester: {{ $semester }} ({{ $semester == 1 ? 'Ganjil' : 'Genap' }})</p>
        </div>

        <!-- Identitas Siswa -->
        <div class="grid grid-cols-2 gap-4 text-xs mb-6 pb-4 border-b border-gray-300">
            <div class="space-y-1.5">
                <div class="flex"><span class="w-32 text-gray-500">Nama Lengkap</span> <span class="font-bold">: {{ $student->name }}</span></div>
                <div class="flex"><span class="w-32 text-gray-500">Nomor Induk (NIS)</span> <span>: {{ $student->student_number ?: '-' }}</span></div>
                <div class="flex"><span class="w-32 text-gray-500">Jenis Kelamin</span> <span>: {{ $student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</span></div>
            </div>
            <div class="space-y-1.5">
                <div class="flex"><span class="w-32 text-gray-500">Kelas</span> <span class="font-semibold">: {{ $student->classRoom?->name ?: '-' }}</span></div>
                <div class="flex"><span class="w-32 text-gray-500">Program</span> <span>: {{ $student->classRoom?->program?->name ?: '-' }}</span></div>
                <div class="flex"><span class="w-32 text-gray-500">Guru Wali Kelas</span> <span>: {{ $student->teacher?->user?->name ?: '-' }}</span></div>
            </div>
        </div>

        <!-- Modul 1: Tahfizh Al-Quran -->
        <div class="mb-6 space-y-3">
            <h3 class="text-sm font-bold uppercase tracking-wider bg-gray-100 p-1.5 border-l-4 border-indigo-700">I. Capaian Tahfizh Al-Qur'an</h3>
            
            <div class="grid grid-cols-3 gap-4 text-xs text-center">
                <div class="border p-2">
                    <span class="block text-gray-500 uppercase tracking-widest text-[9px]">Progres Hafalan</span>
                    <span class="text-base font-bold text-gray-900 mt-1 block">{{ number_format($progress['progress_percent'] ?? 0, 2) }}%</span>
                </div>
                <div class="border p-2">
                    <span class="block text-gray-500 uppercase tracking-widest text-[9px]">Rerata Skor Hafalan</span>
                    <span class="text-base font-bold text-gray-900 mt-1 block">{{ $progress['average_hafalan_score'] > 0 ? round($progress['average_hafalan_score'], 1) : '-' }}</span>
                </div>
                <div class="border p-2">
                    <span class="block text-gray-500 uppercase tracking-widest text-[9px]">Target Terlambat</span>
                    <span class="text-base font-bold text-red-600 mt-1 block">{{ $progress['overdue_targets'] ?? 0 }} Target</span>
                </div>
            </div>

            <!-- Recent Memorizations -->
            @if($hafalanRecords->isNotEmpty())
                <div class="text-xs pt-1">
                    <span class="font-bold text-gray-700 block mb-1">Setoran Hafalan Terakhir:</span>
                    <table class="w-full text-left border">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="p-1 border-r text-center w-10">No</th>
                                <th class="p-1 border-r">Surah / Ayat</th>
                                <th class="p-1 border-r text-center w-24">Tanggal</th>
                                <th class="p-1 text-center w-20">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hafalanRecords as $idx => $record)
                                <tr class="border-b">
                                    <td class="p-1 border-r text-center">{{ $idx + 1 }}</td>
                                    <td class="p-1 border-r">{{ $record->surah?->name_latin }} (Ayat {{ $record->ayah_start }}-{{ $record->ayah_end }})</td>
                                    <td class="p-1 border-r text-center">{{ $record->submitted_at?->format('d/m/Y') }}</td>
                                    <td class="p-1 text-center font-bold">{{ $record->score }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Modul 2: Adab & Akhlak -->
        <div class="mb-6 space-y-3">
            <h3 class="text-sm font-bold uppercase tracking-wider bg-gray-100 p-1.5 border-l-4 border-teal-600">II. Evaluasi Adab & Pembiasaan Akhlak</h3>
            
            <div class="flex justify-between items-center border p-3 text-xs mb-2 bg-teal-50/10">
                <span class="font-bold text-gray-700">RATA-RATA NILAI KEPATUHAN ADAB:</span>
                <span class="text-base font-extrabold text-teal-700">{{ $avgTotal }} / 100</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs text-center">
                <div class="border p-2">
                    <span class="block text-gray-500 mb-1">Adab kpd Allah</span>
                    <span class="font-bold text-gray-900 text-sm">{{ $avgAllah }}%</span>
                </div>
                <div class="border p-2">
                    <span class="block text-gray-500 mb-1">Adab kpd Rasulullah</span>
                    <span class="font-bold text-gray-900 text-sm">{{ $avgRasul }}%</span>
                </div>
                <div class="border p-2">
                    <span class="block text-gray-500 mb-1">Adab Pergaulan</span>
                    <span class="font-bold text-gray-900 text-sm">{{ $avgSosial }}%</span>
                </div>
                <div class="border p-2">
                    <span class="block text-gray-500 mb-1">Adab kpd Al-Qur'an</span>
                    <span class="font-bold text-gray-900 text-sm">{{ $avgQuran }}%</span>
                </div>
            </div>
        </div>

        <!-- Modul 3: Kedisiplinan & Penghargaan (Tanse) -->
        <div class="mb-6 space-y-3">
            <h3 class="text-sm font-bold uppercase tracking-wider bg-gray-100 p-1.5 border-l-4 border-red-600">III. Catatan Kedisiplinan & Prestasi</h3>

            <div class="grid grid-cols-2 gap-4 text-xs text-center mb-3">
                <div class="border p-2 bg-rose-50/10">
                    <span class="block text-rose-500 font-semibold uppercase text-[9px] tracking-wide">Poin Pelanggaran</span>
                    <span class="text-base font-bold text-rose-700 mt-1 block">{{ $violations->sum('points') }} Poin</span>
                </div>
                <div class="border p-2 bg-emerald-50/10">
                    <span class="block text-emerald-500 font-semibold uppercase text-[9px] tracking-wide">Poin Penghargaan (Prestasi)</span>
                    <span class="text-base font-bold text-emerald-700 mt-1 block">+{{ $rewards->sum('points') }} Poin</span>
                </div>
            </div>

            <!-- Violations details -->
            @if($violations->isNotEmpty())
                <div class="text-xs pt-1">
                    <span class="font-bold text-gray-700 block mb-1">Catatan Kasus Pelanggaran:</span>
                    <table class="w-full text-left border">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="p-1 border-r text-center w-10">No</th>
                                <th class="p-1 border-r">Pelanggaran / Sanksi</th>
                                <th class="p-1 border-r text-center w-24">Lokasi</th>
                                <th class="p-1 text-center w-20">Poin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($violations as $idx => $v)
                                <tr class="border-b">
                                    <td class="p-1 border-r text-center">{{ $idx + 1 }}</td>
                                    <td class="p-1 border-r">
                                        <div class="font-semibold">{{ $v->title }}</div>
                                        @if($v->sanction)
                                            <p class="text-[10px] text-amber-700 italic">Sanksi: {{ $v->sanction }}</p>
                                        @endif
                                    </td>
                                    <td class="p-1 border-r text-center">{{ $v->location ?: '-' }}</td>
                                    <td class="p-1 text-center text-red-650 font-bold">-{{ $v->points }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Catatan Wali Kelas -->
        <div class="mb-8 p-4 border border-gray-300 rounded-none text-xs">
            <h4 class="font-bold text-gray-900 uppercase tracking-wider mb-2">CATATAN & EVALUASI WALI KELAS:</h4>
            <p class="italic text-gray-800 leading-relaxed font-semibold">
                "{{ $report?->teacher_notes ?: 'Belum ada catatan deskriptif dari wali kelas.' }}"
            </p>
        </div>

        <!-- Signature Area -->
        <div class="grid grid-cols-3 gap-6 text-center text-xs mt-12 pt-8 border-t border-gray-200">
            <div>
                <p>Mengetahui,</p>
                <p class="font-semibold">Orang Tua / Wali Santri</p>
                <div class="h-20"></div>
                <p class="border-b border-black w-3/4 mx-auto"></p>
            </div>
            <div>
                <p>Sukoharjo, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p class="font-semibold">Guru Wali Kelas</p>
                <div class="h-20"></div>
                <p class="font-bold border-b border-black w-3/4 mx-auto">{{ $student->teacher?->user?->name ?? '.......................' }}</p>
            </div>
            <div>
                <p>Mengetahui,</p>
                <p class="font-semibold">Kepala Sekolah</p>
                <div class="h-20"></div>
                <p class="font-bold border-b border-black w-3/4 mx-auto">Moh. Pandoyo, S.Si, M.Pd, G.r</p>
            </div>
        </div>

    </div>

    <!-- Auto Print Trigger script -->
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            // Auto open print dialog
            setTimeout(() => {
                window.print();
            }, 800);
        });
    </script>
</body>
</html>
