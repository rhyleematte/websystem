<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_type',
        'text_content',
        'mood',
        'hashtags',
    ];

    /** Return hashtags as a clean array of strings (without the #). */
    public function getHashtagsArrayAttribute(): array
    {
        if (!$this->hashtags)
            return [];
        return array_values(array_filter(
            array_map('trim', explode(',', $this->hashtags))
        ));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class)->whereNull('parent_comment_id')->with('user', 'replies.user')->latest();
    }

    public function allComments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class)->orderBy('sort_order');
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
}
