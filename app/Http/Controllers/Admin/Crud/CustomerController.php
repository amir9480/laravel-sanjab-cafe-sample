<?php

namespace App\Http\Controllers\Admin\Crud;

use App\Customer;
use App\Transaction;
use App\Cards\CustomerCard;
use Illuminate\Http\Request;
use Sanjab\Widgets\IdWidget;
use Sanjab\Widgets\TextWidget;
use Sanjab\Widgets\MoneyWidget;
use Sanjab\Widgets\NumberWidget;
use Sanjab\Helpers\MaterialIcons;
use Sanjab\Helpers\CrudProperties;
use Illuminate\Support\Facades\Route;
use Sanjab\Controllers\CrudController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class CustomerController extends CrudController
{
    protected static function properties(): CrudProperties
    {
        return CrudProperties::create('customers')
                ->model(\App\Customer::class)
                ->title("مشتری")
                ->titles("مشتریان")
                ->icon(MaterialIcons::PEOPLE)
                ->creatable(false)
                ->editable(false);
    }

    protected function init(string $type, Model $item = null): void
    {
        $this->cards = [CustomerCard::create('مدیریت')];

        $this->widgets[] = IdWidget::create();

        $this->widgets[] = TextWidget::create('mobile', 'شماره همراه')
                            ->rules('required|iran_mobile');

        $this->widgets[] = NumberWidget::create('coin', 'سکه')
                            ->required();

        $this->widgets[] = MoneyWidget::create('total_buy', 'مجموع خرید')
                            ->required()
                            ->postfix("");
    }

    /**
     * Find or create customer.
     *
     * @param Request $request
     * @return array
     */
    public function search(Request $request)
    {
        $query = Customer::where('mobile', $request->input('mobile'))
                        ->orWhere('id', $request->input('id'))
                        ->withCount(['transactions', 'transactions as last_month_transactions_count' => function (Builder $query) {
                            $query->where('created_at', '>', now()->subDays(30));
                        }]);
        $customer = $query->first();
        if ($customer == null) {
            if ($request->filled('id')) {
                throw ValidationException::withMessages(['id' => 'مشتری با شماره عضویت '.$request->input('id').' یافت نشد.']);
            }
            $request->validate(['mobile' => 'required|iran_mobile']);
            $customer = Customer::create($request->only('mobile'));
            $customer = $query->first();
            return ['newCreated' => true, 'customer' => $customer];
        }
        return ['customer' => $customer];
    }

    /**
     * Submit a buy for customer.
     *
     * @param Request $request
     * @param Customer $customer
     * @return array
     */
    public function buy(Request $request, Customer $customer)
    {
        $customer->loadTransactionInformations();
        if ($request->input('price') == 0) {
            $request->merge(['price' => null]);
        }
        if ($request->input('coin') == 0) {
            $request->merge(['coin' => null]);
        }
        $request->validate([
            'price' => 'required_without:coin|nullable|numeric|min:1000',
            'coin' => 'required_without:price|nullable|numeric|min:1|max:'.$customer->coin,
        ], [
            'price.required_without' => 'فیلد نقد و سکه خالی است.',
            'coin.required_without' => 'فیلد نقد و سکه خالی است.',
        ]);
        $customer->transactions()->create(['money' => intval($request->input('price', 0)), 'coin' => intval($request->input('coin', 0))]);
        $coinPoint = $this->getMoneyPoint(intval($request->input('price')));
        $coin = $coinPoint - intval($request->input('coin', 0));
        if ($coin != 0) {
            $customer->{ $coin > 0 ? 'increment' : 'decrement' }('coin', abs($coin));
        }
        $customer->refresh();
        $customer->loadTransactionInformations();
        return [
            'customer' => $customer,
            'message' => 'خرید با موفقیت انجام شد.'.($coinPoint > 0 ? "\nدست آورد سکه: $coinPoint" : '')
        ];
    }

    /**
     * Get transactions of a customer.
     *
     * @param Customer $customer
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function transactions(Customer $customer)
    {
        $transactions = $customer->transactions()->latest()->paginate(10);
        foreach ($transactions as $transaction) {
            $transaction->can_delete = $transaction->created_at > now()->subHour();
        }
        return $transactions;
    }

    /**
     * Delete a wrong transaction.
     *
     * @param Transaction $transaction
     * @return array
     */
    public function deleteTransaction(Transaction $transaction)
    {
        if ($transaction->created_at <= now()->subHour()) {
            return response()->json(['message' => 'شما اجازه حذف این تاریخچه را ندارید.'], 403);
        }
        $customer = $transaction->customer;
        $customer->total_buy = max($customer->total_buy - $transaction->money, 0);
        $customer->coin -= $this->getMoneyPoint($transaction->money);
        $customer->coin = max($customer->coin + $transaction->coin, 0);
        $customer->save();
        $transaction->delete();
        $customer->refresh();
        $customer->loadTransactionInformations();
        return [
            'success' => true,
            'customer' => $customer
        ];
    }

    public static function routes(): void
    {
        parent::routes();
        Route::prefix("modules/customers")->name("modules.customers.")->group(function () {
            Route::post('search', static::class.'@search')->name('search');
            Route::get('transactions/{customer}', static::class.'@transactions')->name('transactions');
            Route::delete('transactions/{transaction}', static::class.'@deleteTransaction')->name('transactions.delete');
            Route::post('buy/{customer}', static::class.'@buy')->name('buy');
        });
    }

    /**
     * Get point of buy.
     *
     * @param int $pricePoint
     * @return int
     */
    private function getMoneyPoint($pricePoint)
    {
        $coinPoint = 0;
        if ($pricePoint > 0) {
            foreach (setting('point.ranges') as $value) {
                if ($value['min'] <= $pricePoint && $value['max'] > $pricePoint) {
                    $coinPoint = $value['point'];
                }
            }
        }
        return $coinPoint;
    }
}
