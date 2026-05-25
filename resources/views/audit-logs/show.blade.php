<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Audit Log
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Informasi lengkap aktivitas sistem.
                </p>
            </div>

            <a href="{{ route('audit-logs.index') }}"
               class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    Ringkasan
                </h3>

                <dl class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Waktu</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $auditLog->created_at?->format('d M Y H:i:s') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Event</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $auditLog->event_label }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">User</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $auditLog->user?->name ?? 'System' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email User</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $auditLog->user?->email ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Model</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $auditLog->auditable_type_label }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Objek</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $auditLog->auditable_label ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Auditable ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $auditLog->auditable_id ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $auditLog->ip_address ?? '-' }}
                        </dd>
                    </div>

                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Auditable Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 break-all">
                            {{ $auditLog->auditable_type ?? '-' }}
                        </dd>
                    </div>

                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">URL</dt>
                        <dd class="mt-1 text-sm text-gray-900 break-all">
                            {{ $auditLog->url ?? '-' }}
                        </dd>
                    </div>

                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                        <dd class="mt-1 text-sm text-gray-900 break-all">
                            {{ $auditLog->user_agent ?? '-' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Nilai Lama
                    </h3>

                    <pre class="mt-4 overflow-x-auto rounded-lg bg-gray-900 p-4 text-xs text-white">{{ json_encode($auditLog->old_values ?: [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Nilai Baru
                    </h3>

                    <pre class="mt-4 overflow-x-auto rounded-lg bg-gray-900 p-4 text-xs text-white">{{ json_encode($auditLog->new_values ?: [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>