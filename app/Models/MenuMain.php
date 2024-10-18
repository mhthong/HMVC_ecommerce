<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class MenuMain extends Model
{
    use HasFactory;

    protected $table = 'menu_main';

    protected $fillable = ['name', 'key'];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'main_id');
    }

}
