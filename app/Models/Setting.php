<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{

    use HasFactory;

    protected $fillable = ['key', 'value'];

    // Optionally, if you want to use timestamps
    public $timestamps = true;
}
