<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'username',
        'fname',
        'mname',
        'lname',
        'gender',
        'bday',
        'role',
        'doctor_status',
        'profile_photo',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────
    public function posts()
    {
        return $this->hasMany(Post::class)->with(['likes', 'comments', 'media'])->latest();
    }

    public function postLikes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function postComments()
    {
        return $this->hasMany(PostComment::class);
    }

    // ── Accessors ─────────────────────────────────────────────────
    public function getFullNameAttribute(): string
    {
        // Normalize each part: first letter uppercase, rest lowercase
        $parts = [];
        foreach (['fname', 'mname', 'lname'] as $field) {
            $val = $this->{ $field};
            if ($val && trim($val) !== '') {
                $parts[] = $this->toTitleCase(trim($val));
            }
        }
        return implode(' ', $parts);
    }

    public function getShortNameAttribute(): string
    {
        $parts = [];

        if ($this->fname && trim($this->fname) !== '') {
            $fnameParts = explode(' ', trim($this->fname));
            $parts[] = $this->toTitleCase($fnameParts[0]);
        }

        if ($this->mname && trim($this->mname) !== '') {
            $initial = mb_substr(trim($this->mname), 0, 1, 'UTF-8');
            $parts[] = mb_strtoupper($initial, 'UTF-8') . '.';
        }

        if ($this->lname && trim($this->lname) !== '') {
            $parts[] = $this->toTitleCase(trim($this->lname));
        }

        return implode(' ', $parts);
    }

    /**
     * Converts a string to Title Case — each word's first letter
     * uppercase, the rest lowercase. Handles compound names with hyphens.
     */
    private function toTitleCase(string $str): string
    {
        // mb_convert_case handles Unicode (e.g. Filipino names)
        return mb_convert_case(mb_strtolower($str), MB_CASE_TITLE, 'UTF-8');
    }

    public function getAvatarUrlAttribute(): string
    {
        $photo = $this->profile_photo;

        if (!$photo || $photo === 'profiles/default.png') {
            return asset('assets/img/default.png');
        }

        if (strpos($photo, 'http') === 0) {
            return $photo;
        }

        return asset('storage/' . ltrim($photo, '/'));
    }
}
