<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'auditable_type',
        'auditable_id',
        'auditable_label',
        'event',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            'created' => 'Dibuat',
            'updated' => 'Diubah',
            'deleted' => 'Dihapus',
            'restored' => 'Dipulihkan',
            'force_deleted' => 'Dihapus Permanen',
            default => ucfirst((string) $this->event),
        };
    }

    public function getActionAttribute(): ?string
    {
        return $this->event;
    }

    public function getActionLabelAttribute(): string
    {
        return $this->event_label;
    }

    public function getAuditableNameAttribute(): string
    {
        return $this->auditable_label
            ?: class_basename((string) $this->auditable_type) . ' #' . $this->auditable_id;
    }

    public function getAuditableTypeLabelAttribute(): string
    {
        return match ($this->auditable_type) {
            HafalanRecord::class => 'Setoran Hafalan',
            MurajaahRecord::class => 'Murajaah',
            HafalanTarget::class => 'Target Hafalan',
            Student::class => 'Santri',
            Program::class => 'Program',
            ClassRoom::class => 'Kelas',
            TeacherProfile::class => 'Guru',
            ParentProfile::class => 'Orangtua',
            User::class => 'User',
            default => class_basename((string) $this->auditable_type),
        };
    }
}