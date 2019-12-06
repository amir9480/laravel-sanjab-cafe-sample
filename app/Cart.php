<?php

namespace App;

use Baloot\EloquentHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Cart extends Model
{
    use EloquentHelper;

    protected $fillable = ['seen', 'customer_id', 'date'];
    protected $casts = [
        'seen' => 'bool',
        'date' => 'date',
    ];

    /* -------------------------------- Relations ------------------------------- */

    /**
     * Owner of cart.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Products inside this cart.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)
                    ->withPivot('quantity')
                    ->using(CartProduct::class);
    }

    /* --------------------------------- Scopes --------------------------------- */

    /**
     * Add total price of products.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithTotal(Builder $query)
    {
        return $query->selectRaw("carts.*, SUM(products.price * cart_product.quantity) as total")
                ->join('cart_product', 'cart_product.cart_id', '=', 'carts.id')
                ->join('products', 'cart_product.product_id', '=', 'products.id')
                ->groupBy('carts.id');
    }

}
