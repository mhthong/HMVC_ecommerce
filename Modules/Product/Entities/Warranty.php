<?php 
namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Warranty extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
    * @var string
    */
   protected $table = 'warranties';

   /**
    * @var array
    */

    protected $fillable = [
        'product_id',
        'warranty_code',
        'status',
        'active_date',
        'customer_id'
    ];

    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship with Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
