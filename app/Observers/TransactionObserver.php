<?php

namespace App\Observers;

use App\Customer;
use App\Transaction;

class TransactionObserver
{
    /**
     * Handle the transaction "saved" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function saved(Transaction $transaction)
    {
        $this->updateCustomerTotalBuy($transaction->customer);
    }

    /**
     * Handle the transaction "deleted" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function deleted(Transaction $transaction)
    {
        $this->updateCustomerTotalBuy($transaction->customer);
    }

    /**
     * Update total buy of customer
     *
     * @param Customer $customer
     * @return void
     */
    private function updateCustomerTotalBuy(Customer  $customer)
    {
        $customer->total_buy = $customer->transactions()->sum('money');
        $customer->save();
    }
}
