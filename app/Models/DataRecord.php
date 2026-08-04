<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataRecord extends Model
{
    use HasFactory;

    protected $fillable = ['data_model_id', 'values', 'created_by'];

    protected $casts = [
        'values' => 'array',
    ];

    public function dataModel(): BelongsTo
    {
        return $this->belongsTo(DataModel::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}