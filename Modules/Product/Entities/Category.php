<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

        /**
     * The database table used by the model.
     *
    * @var string
    */
   protected $table = 'categories';

   /**
    * @var array
    */

    protected $fillable = ['name', 'description', 'parent_id','is_featured'];
    
    // Many-to-many relationship between categories and products
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }
    
      // Mối quan hệ parent - category con (đa cấp)
      public function parent()
      {
          return $this->belongsTo(Category::class, 'parent_id');
      }
  
      public function children()
      {
          return $this->hasMany(Category::class, 'parent_id');
      }
      
    
/*     protected static function newFactory()
    {
        return \Modules\Product\Database\factories\CategoryFactory::new();
    } */
}
