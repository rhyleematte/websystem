<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'guidelines',
        'cover_photo',
        'creator_id',
        'visibility',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class , 'creator_id');
    }

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class)->with(['likes', 'comments', 'media'])->latest();
    }
}
