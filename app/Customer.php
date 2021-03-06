<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Customer extends Model
{
    protected $fillable = [
        'mobile'
    ];

    protected $casts = [
        'coin' => 'int',
        'total_buy' => 'int',
    ];

    protected $appends = [
        'date'
    ];

    /* -------------------------------- Relations ------------------------------- */

    /**
     * Transactions of customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Carts of customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    /* -------------------------------- Mutators -------------------------------- */

    /**
     * Get created_at in jalali format
     *
     * @return string
     */
    public function getDateAttribute()
    {
        return $this->created_at_fa_f;
    }

    /* -------------------------------- Functions ------------------------------- */

    /**
     * Load Transactions informations
     *
     * @return void
     */
    public function loadTransactionInformations()
    {
        $this->loadCount(['transactions', 'transactions as last_month_transactions_count' => function (Builder $query) {
            $query->where('created_at', '>', now()->subDays(30));
        }]);
    }
}
