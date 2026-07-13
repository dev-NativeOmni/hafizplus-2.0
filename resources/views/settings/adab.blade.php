<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-zinc-150 leading-tight">
                Pengaturan Kuisioner Adab
            </h2>
            <p class="text-sm text-gray-600 dark:text-zinc-400">
                Sesuaikan teks 4 kategori adab dan 5 butir pertanyaan di setiap kategori (total 20 pertanyaan).
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:bg-emerald-950/40 dark:border-emerald-800/60 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 dark:bg-red-950/40 dark:border-red-800/60 dark:text-red-300">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('settings.adab.update') }}" class="space-y-6">
                @csrf

                @foreach ($categories as $catIdx => $category)
                    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                        {{-- Header Kategori --}}
                        <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-4 bg-gray-50/50 dark:bg-[#09090b]/40 space-y-3">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="inline-flex items-center justify-center h-7 w-7 rounded-full bg-indigo-100 dark:bg-indigo-950/40 text-sm font-bold text-indigo-700 dark:text-indigo-400">
                                    {{ $catIdx + 1 }}
                                </span>
                                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Kategori {{ $catIdx + 1 }}</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-550 dark:text-zinc-400">Nama Kategori</label>
                                    <input
                                        type="text"
                                        name="categories[{{ $catIdx }}][title]"
                                        value="{{ old("categories.{$catIdx}.title", $category['title']) }}"
                                        class="block w-full rounded-xl border-gray-300 dark:border-zinc-700 dark:bg-[#09090b]/40 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold"
                                        required
                                    />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-550 dark:text-zinc-400">Deskripsi Kategori</label>
                                    <input
                                        type="text"
                                        name="categories[{{ $catIdx }}][desc]"
                                        value="{{ old("categories.{$catIdx}.desc", $category['desc']) }}"
                                        class="block w-full rounded-xl border-gray-300 dark:border-zinc-700 dark:bg-[#09090b]/40 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                        required
                                    />
                                </div>
                            </div>
                        </div>

                        {{-- 5 Pertanyaan --}}
                        <div class="p-6 space-y-4">
                            <h4 class="text-xs font-bold text-gray-400 dark:text-zinc-550 uppercase tracking-widest border-b pb-2">5 Butir Pertanyaan (Evaluasi Harian)</h4>
                            <div class="space-y-4">
                                @for ($qIdx = 0; $qIdx < 5; $qIdx++)
                                    <div class="flex items-start gap-4">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-50 dark:bg-indigo-950/40 text-xs font-bold text-indigo-600 dark:text-indigo-400 shrink-0 mt-1">
                                            {{ ($catIdx * 5) + $qIdx + 1 }}
                                        </span>
                                        <div class="flex-1">
                                            <input
                                                type="text"
                                                name="categories[{{ $catIdx }}][questions][{{ $qIdx }}]"
                                                value="{{ old("categories.{$catIdx}.questions.{$qIdx}", $category['questions'][$qIdx] ?? '') }}"
                                                placeholder="Pertanyaan nomor {{ ($catIdx * 5) + $qIdx + 1 }}..."
                                                class="block w-full rounded-xl border-gray-300 dark:border-zinc-700 dark:bg-[#09090b]/40 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                required
                                            />
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Keterangan Nilai Huruf --}}
                <div class="bg-indigo-50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/30 rounded-xl px-6 py-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-700 dark:text-indigo-400 mb-3">📊 Konversi Nilai Huruf (Skala 0–100%)</h4>
                    <div class="grid grid-cols-5 gap-3 text-center text-xs">
                        @foreach (['A'=>['90–100%','bg-emerald-100 text-emerald-700'], 'B'=>['80–89%','bg-teal-100 text-teal-700'], 'C'=>['70–79%','bg-amber-100 text-amber-700'], 'D'=>['60–69%','bg-orange-100 text-orange-700'], 'E'=>['0–59%','bg-rose-100 text-rose-700']] as $g => [$range, $cls])
                            <div class="rounded-lg p-3 {{ $cls }} dark:opacity-80">
                                <div class="text-2xl font-black">{{ $g }}</div>
                                <div class="font-semibold mt-1">{{ $range }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Tombol Simpan --}}
                <div class="pt-4 border-t border-gray-200 dark:border-zinc-800 flex justify-end gap-3">
                    <a href="{{ route('adab.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-gray-300 dark:border-zinc-700 rounded-xl text-sm font-semibold text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all duration-150">
                        Simpan Semua Pertanyaan
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
