<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DataField extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_model_id',
        'name',
        'key',
        'type',
        'options',
        'is_required',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    // Tipe field yang didukung. Tambahkan di sini kalau mau nambah tipe baru nanti.
    public const TYPES = ['text', 'textarea', 'number', 'date', 'boolean', 'select'];

    protected static function booted(): void
    {
        static::creating(function (DataField $field) {
            if (empty($field->key)) {
                $field->key = Str::snake($field->name);
            }
        });
    }

    public function dataModel(): BelongsTo
    {
        return $this->belongsTo(DataModel::class);
    }
}