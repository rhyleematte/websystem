<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiGuideline extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'file_path',
        'original_filename',
        'is_parsed',
    ];
}
