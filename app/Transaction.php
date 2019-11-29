<?php

namespace App;

use Baloot\EloquentHelper;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use EloquentHelper;

    protected $fillable = [
        'money', 'coin'
    ];

    protected $appends = [
        'date'
    ];

    /**
     * Customer done this transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get created_at in jalali format
     *
     * @return string
     */
    public function getDateAttribute()
    {
        return $this->created_at_fa_ft;
    }
}
