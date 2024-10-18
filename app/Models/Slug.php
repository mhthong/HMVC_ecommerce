<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Slug extends Model
{
    use HasFactory;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'slugs';

    /**
     * @var array
     */
    protected $fillable = [
        'key',
        'reference_id',
        'reference',
        'prefix',
    ];

    public function menu(): HasOne
    {
        return $this->hasOne(Menu::class, 'slug_id');
    }

}
