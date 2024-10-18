<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ForeignImage extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
    * @var string
    */
   protected $table = 'foreign_images';

   /**
    * @var array
    */


    protected $fillable = ['product_id', 'image'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
/*     protected static function newFactory()
    {
        return \Modules\Product\Database\factories\ForeignImageFactory::new();
    } */
}
