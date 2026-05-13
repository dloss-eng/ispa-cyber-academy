<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resource extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'type',
        'file_path',
        'download_count',
    ];

    protected $guarded = [];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = trim($value);
    }

    public function setTypeAttribute($value)
    {
        $allowed = ['pdf', 'video', 'doc'];
        $this->attributes['type'] = in_array($value, $allowed) ? $value : 'pdf';
    }

    public function incrementDownload(): void
    {
        $this->increment('download_count');
    }
}
