<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';

    protected $fillable = ['name', 'link', 'main_id', 'parent_id', 'order', 'slug_id'];

    // Quan hệ với MenuMain
    public function main(): BelongsTo
    {
        return $this->belongsTo(MenuMain::class, 'main_id');
    }

    // Quan hệ với Slug
    public function slug(): BelongsTo
    {
        return $this->belongsTo(Slug::class, 'slug_id');
    }

    // Quan hệ cha
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    // Quan hệ con
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }
}
