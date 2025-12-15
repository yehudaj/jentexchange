<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entertainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','stage_name','bio','price_usd','pricing_notes','genres',
        'types','audiences','cities','pricing_packages','video_links','profile_image_path','background_image_path'
    ];

    protected $casts = [
        'genres' => 'array',
        'types' => 'array',
        'audiences' => 'array',
        'cities' => 'array',
        'video_links' => 'array',
        'pricing_packages' => 'array',
        'price_usd' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProfileImageUrlAttribute()
    {
        if (empty($this->profile_image_path)) return null;
        return \Illuminate\Support\Facades\Storage::url($this->profile_image_path);
    }

    public function getBackgroundImageUrlAttribute()
    {
        if (empty($this->background_image_path)) return null;
        return \Illuminate\Support\Facades\Storage::url($this->background_image_path);
    }
}
