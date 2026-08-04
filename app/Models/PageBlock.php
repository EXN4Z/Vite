<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageBlock extends Model
{
    use HasFactory;

    protected $fillable = ['page_id', 'type', 'config', 'order'];

    protected $casts = [
        'config' => 'array',
    ];

    // Tipe blok yang didukung. Tambahkan di sini kalau mau nambah blok baru nanti.
    public const TYPES = ['text', 'table', 'chart', 'card'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}