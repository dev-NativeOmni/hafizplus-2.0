<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('adab-materials.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-zinc-450 dark:hover:text-zinc-300">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold leading-tight text-gray-900 dark:text-white">
                    Edit Materi Adab
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-zinc-400">
                    Perbarui judul, deskripsi, berkas dokumen, atau tautan eksternal materi.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-gray-250 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm p-6">
                
                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 dark:bg-red-950/20 dark:border-red-900/30 px-4 py-3 text-sm text-red-600 dark:text-red-400">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('adab-materials.update', $adabMaterial) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-xs font-bold text-gray-750 dark:text-zinc-300 uppercase tracking-wider mb-2">Judul Materi <span class="text-red-500">*</span></label>
                        <input type="text"
                               id="title"
                               name="title"
                               value="{{ old('title', $adabMaterial->title) }}"
                               placeholder="Contoh: Adab terhadap Orang Tua dan Guru"
                               class="w-full rounded-xl border-gray-300 dark:border-zinc-700 bg-transparent text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white"
                               required
                        />
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-xs font-bold text-gray-750 dark:text-zinc-300 uppercase tracking-wider mb-2">Ringkasan / Deskripsi Materi</label>
                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  placeholder="Tulis garis besar materi halaqoh adab di sini..."
                                  class="w-full rounded-xl border-gray-300 dark:border-zinc-700 bg-transparent text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white"
                        >{{ old('description', $adabMaterial->description) }}</textarea>
                    </div>

                    <!-- Active File Info -->
                    @if($adabMaterial->file_path)
                        <div class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-850/50 p-4 space-y-3">
                            <h4 class="text-xs font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Berkas Dokumen Aktif</h4>
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-700 dark:text-zinc-300">{{ $adabMaterial->file_name }}</span>
                                </div>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-bold text-red-600 dark:text-red-400">
                                    <input type="checkbox" name="remove_file" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500" />
                                    Hapus Berkas Ini
                                </label>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- File Upload -->
                        <div>
                            <label for="file" class="block text-xs font-bold text-gray-750 dark:text-zinc-300 uppercase tracking-wider mb-2">
                                {{ $adabMaterial->file_path ? 'Ganti File Dokumen' : 'Upload File Dokumen' }}
                            </label>
                            <input type="file"
                                   id="file"
                                   name="file"
                                   class="w-full rounded-xl border border-gray-300 dark:border-zinc-700 bg-transparent text-sm text-gray-500 dark:text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-600 dark:file:bg-zinc-800 dark:file:text-zinc-300 file:hover:bg-indigo-100 cursor-pointer"
                            />
                            <p class="mt-2 text-[10px] text-gray-500 dark:text-zinc-550 leading-normal">
                                Format: PDF, Doc, Excel, PPT, Gambar, ZIP. Maksimal 5MB.
                            </p>
                        </div>

                        <!-- URL Link -->
                        <div>
                            <label for="url_link" class="block text-xs font-bold text-gray-750 dark:text-zinc-300 uppercase tracking-wider mb-2">Link Tautan Eksternal</label>
                            <input type="url"
                                   id="url_link"
                                   name="url_link"
                                   value="{{ old('url_link', $adabMaterial->url_link) }}"
                                   placeholder="Contoh: https://youtube.com/... atau Drive"
                                   class="w-full rounded-xl border-gray-300 dark:border-zinc-700 bg-transparent text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white"
                            />
                            <p class="mt-2 text-[10px] text-gray-500 dark:text-zinc-550 leading-normal">
                                Masukkan link Google Drive, YouTube, atau artikel pendukung lainnya.
                            </p>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-zinc-800">
                        <a href="{{ route('adab-materials.index') }}"
                           class="inline-flex items-center justify-center rounded-xl border border-gray-300 dark:border-zinc-700 px-5 py-2.5 text-sm font-bold text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-800 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 transition">
                            Perbarui Materi
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
