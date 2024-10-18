<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * @var array
     */

    protected $fillable = ['name', 'slug', 'description', 'shortdescription', 'content', 'price', 'priceoff', 'image', 'is_featured', 'discount_id'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function foreignImages()
    {
        return $this->hasMany(ForeignImage::class);
    }

    // Relationship with Discount
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    // Relationship with Warranty
    public function warranties()
    {
        return $this->hasMany(Warranty::class);
    }

    /*     protected static function newFactory()
    {
        return \Modules\Product\Database\factories\ProductFactory::new();
    } */
}
