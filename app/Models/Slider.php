<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sliders';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'key',
        'description',
        'status',
    ];

    /**
     * @var array
     */
/*     protected $casts = [
        'status' => BaseStatusEnum::class,
    ]; */


    public function page()
    {
        return $this->hasOne(Page::class);
    }

    public function post()
    {
        return $this->hasOne(Post::class);
    }

    public function items()
    {
        return $this->hasMany(SliderItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        self::deleting(function (Slider $slider) {
            SliderItem::where('slider_id', $slider->id)->delete();
        });
    }
}

