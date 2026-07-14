<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-gray-900 dark:text-white">
                    Materi Halaqoh Adab
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-zinc-400">
                    Kumpulan berkas materi dan panduan untuk kegiatan halaqoh adab santri.
                </p>
            </div>

            @if($canManage)
                <a href="{{ route('adab-materials.create') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 transition">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Materi
                </a>
            @endif
        </div>
    </x-slot>

    @php
        $formatBytes = function($bytes) {
            if (!$bytes || $bytes <= 0) return '0 B';
            $k = 1024;
            $sizes = ['B', 'KB', 'MB', 'GB'];
            $i = floor(log($bytes) / log($k));
            return number_format($bytes / pow($k, $i), 1) . ' ' . $sizes[$i];
        };
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 dark:bg-green-950/20 dark:border-green-800/30 px-4 py-3 text-sm font-medium text-green-700 dark:text-green-400 flex items-center gap-2">
                    <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filter / Search Section -->
            <div class="rounded-2xl border border-gray-250 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 p-5 shadow-sm">
                <form method="GET" action="{{ route('adab-materials.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <input type="text"
                               name="q"
                               value="{{ request('q') }}"
                               placeholder="Cari berdasarkan judul atau deskripsi materi..."
                               class="w-full rounded-xl border-gray-300 dark:border-zinc-700 bg-transparent text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white"
                        />
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-gray-900 dark:bg-zinc-800 px-5 py-2 text-sm font-semibold text-white hover:bg-gray-800 dark:hover:bg-zinc-700 transition">
                            Cari
                        </button>
                        @if(request('q'))
                            <a href="{{ route('adab-materials.index') }}"
                               class="inline-flex items-center justify-center rounded-xl border border-gray-300 dark:border-zinc-700 px-5 py-2 text-sm font-semibold text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-800 transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Grid Cards -->
            @if($materials->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($materials as $material)
                        <div class="group relative flex flex-col justify-between rounded-2xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5 shadow-sm hover:shadow-md transition">
                            <div class="space-y-3">
                                <!-- Heading Title -->
                                <h3 class="font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition text-base">
                                    {{ $material->title }}
                                </h3>

                                <!-- Description -->
                                @if($material->description)
                                    <p class="text-sm text-gray-600 dark:text-zinc-400 line-clamp-3 leading-relaxed">
                                        {{ $material->description }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-400 dark:text-zinc-600 italic">
                                        Tidak ada deskripsi tambahan.
                                    </p>
                                @endif
                            </div>

                            <!-- Metadata & Actions -->
                            <div class="mt-6 space-y-4 pt-4 border-t border-gray-100 dark:border-zinc-800/80">
                                @if($material->file_path)
                                    <!-- File Info -->
                                    <div class="flex items-center gap-2.5 bg-gray-50 dark:bg-zinc-800/50 p-2.5 rounded-xl border border-gray-100 dark:border-zinc-800">
                                        <svg class="h-8 w-8 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-gray-700 dark:text-zinc-300 truncate">
                                                {{ $material->file_name }}
                                            </p>
                                            <p class="text-[10px] text-gray-400 dark:text-zinc-500 font-mono">
                                                {{ $formatBytes($material->file_size) }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                @if($material->url_link)
                                    <!-- Link Info -->
                                    <a href="{{ $material->url_link }}" target="_blank" rel="noopener noreferrer"
                                       class="flex items-center gap-2 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        Buka Link Eksternal
                                    </a>
                                @endif

                                <!-- Creator & Date -->
                                <div class="flex items-center justify-between text-[11px] text-gray-400 dark:text-zinc-500">
                                    <span>Oleh: <strong class="text-gray-600 dark:text-zinc-400 font-semibold">{{ $material->creator?->name ?? 'Sistem' }}</strong></span>
                                    <span>{{ $material->created_at?->format('d/m/Y') }}</span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2">
                                    @if($material->file_path)
                                        <a href="{{ asset('storage/' . $material->file_path) }}" download="{{ $material->file_name }}"
                                           class="flex-1 inline-flex items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 px-3.5 py-2 text-xs font-bold hover:bg-indigo-100 dark:hover:bg-indigo-950 transition">
                                            <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Unduh
                                        </a>
                                    @endif

                                    @if($canManage)
                                        <a href="{{ route('adab-materials.edit', $material) }}"
                                           class="inline-flex items-center justify-center rounded-xl border border-gray-200 dark:border-zinc-800 text-gray-700 dark:text-zinc-300 p-2 hover:bg-gray-50 dark:hover:bg-zinc-800 transition">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('adab-materials.destroy', $material) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi adab ini? Berkas juga akan terhapus dari sistem.');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center rounded-xl border border-red-200 dark:border-red-950 text-red-600 p-2 hover:bg-red-50 dark:hover:bg-red-950/20 transition">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginator -->
                <div class="mt-6">
                    {{ $materials->links() }}
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-gray-300 dark:border-zinc-800 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-zinc-650" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h3 class="mt-4 text-sm font-bold text-gray-900 dark:text-white">Belum Ada Materi Adab</h3>
                    <p class="mt-2 text-xs text-gray-500 dark:text-zinc-400 max-w-sm mx-auto">
                        Silakan hubungi ustadz/ustadzah pembimbing atau admin untuk mengunggah materi halaqoh adab terbaru.
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
