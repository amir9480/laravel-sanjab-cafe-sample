<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * Transactions of customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get created_at in jalali format
     *
     * @return string
     */
    public function getDateAttribute()
    {
        return $this->created_at_fa_f;
    }

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
