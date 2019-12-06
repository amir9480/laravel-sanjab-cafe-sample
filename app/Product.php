<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'category_id'];
    public $timestamps = false;

    /* -------------------------------- Relations ------------------------------- */

    /**
     * Category of product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Carts that contain this product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function carts()
    {
        return $this->belongsToMany(Cart::class)
                    ->withPivot('quantity')
                    ->using(CartProduct::class);
    }
}
