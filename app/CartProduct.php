<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CartProduct extends Pivot
{
    protected $fillable = ['cart_id', 'product_id', 'quantity'];
    protected $casts = [
        'quantity' => 'int'
    ];
    public $timestamps = false;

    /* -------------------------------- Relations ------------------------------- */

    /**
     * Cart.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
