<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // ── Relasi ─────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helper Static: merekam log dengan mudah ────────────────────────────────

    /**
     * Buat entri audit log baru.
     *
     * Contoh:
     *   AuditLog::record('update', 'AlertRule', 5, 'Mengubah aturan suhu ruangan');
     */
    public static function record(
        string  $action,
        ?string $modelType  = null,
        mixed   $modelId    = null,
        string  $description = '',
        ?array  $oldValues  = null,
        ?array  $newValues  = null,
    ): self {
        return static::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'description' => $description,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }

    // ── Label warna untuk badge action ────────────────────────────────────────

    public function getActionBadgeAttribute(): array
    {
        return match($this->action) {
            'create'  => ['label' => 'Tambah',  'class' => 'text-green-600 bg-green-50'],
            'update'  => ['label' => 'Ubah',    'class' => 'text-blue-600 bg-blue-50'],
            'delete'  => ['label' => 'Hapus',   'class' => 'text-red-600 bg-red-50'],
            'login'   => ['label' => 'Login',   'class' => 'text-indigo-600 bg-indigo-50'],
            'logout'  => ['label' => 'Logout',  'class' => 'text-slate-600 bg-slate-100'],
            'export'  => ['label' => 'Ekspor',  'class' => 'text-orange-600 bg-orange-50'],
            default   => ['label' => ucfirst($this->action), 'class' => 'text-slate-600 bg-slate-100'],
        };
    }
}
