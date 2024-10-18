<?php 
namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
    * @var string
    */
   protected $table = 'customers';

   /**
    * @var array
    */

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'province',
        'city',
        'district',
    ];

    // Relationship with Warranty
    public function warranties()
    {
        return $this->hasMany(Warranty::class);
    }
}
