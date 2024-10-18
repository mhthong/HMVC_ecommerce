<?php
namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
    * @var string
    */
   protected $table = 'discounts';

   /**
    * @var array
    */

    protected $fillable = [
        'code',
        'value',
        'unit'
    ];

    // Relationship with Products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
