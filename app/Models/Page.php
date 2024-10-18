<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

        /**
     * The database table used by the model.
     *
    * @var string
    */
   protected $table = 'pages';

   /**
    * @var array
    */
   protected $fillable = [
       'name',
       'content',
       'user_id',
       'image',
       'template',
       'description',
       'is_featured',
       'status',
       'slug',
       'slider_id'    
   ];

   public function posts()
   {
       return $this->belongsToMany(Post::class, 'page_post');
   }

   public function slider()
   {
       return $this->belongsTo(Slider::class);
   }

   
					
}
