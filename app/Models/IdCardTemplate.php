<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class IdCardTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'institute_id',
        'category_id',
        'name',
        'type',
        'status',
        'orientation',
        'front_layout_json',
        'back_layout_json',
    ];

    protected $casts = [
        'front_layout_json' => 'array',
        'back_layout_json' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }
}
