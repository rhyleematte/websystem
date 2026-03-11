<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'content',
        'file_path',
        'file_type',
        'thumbnail',
        'duration_meta',
        'hashtags',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /** Return hashtags as clean array */
    public function getHashtagsArrayAttribute(): array
    {
        if (!$this->hashtags) return [];
        return array_values(array_filter(
            array_map('trim', explode(',', $this->hashtags))
        ));
    }

    /** Get thumbnail URL */
    public function getThumbnailUrlAttribute(): string
    {
        if (!$this->thumbnail) {
            return asset('assets/img/default-resource.png');
        }
        return asset('storage/' . $this->thumbnail);
    }

    /** Get File URL */
    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) return null;
        return asset('storage/' . $this->file_path);
    }
}
