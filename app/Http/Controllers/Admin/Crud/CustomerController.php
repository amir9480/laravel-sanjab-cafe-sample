<?php

namespace App\Http\Controllers\Admin\Crud;

use App\Cards\CustomerCard;
use App\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Sanjab\Controllers\CrudController;
use Sanjab\Helpers\CrudProperties;
use Sanjab\Helpers\MaterialIcons;
use Sanjab\Widgets\IdWidget;
use Sanjab\Widgets\NumberWidget;
use Sanjab\Widgets\TextWidget;

class CustomerController extends CrudController
{
    protected static function properties(): CrudProperties
    {
        return CrudProperties::create('customers')
                ->model(\App\Customer::class)
                ->title("مشتری")
                ->titles("مشتریان")
                ->icon(MaterialIcons::ATTACH_MONEY)
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

        $this->widgets[] = NumberWidget::create('point', 'امتیاز')
                            ->required();
    }

    public function search(Request $request)
    {
        $customer = Customer::where('mobile', $request->input('mobile'))->orWhere('id', $request->input('id'))->first();
        if ($customer == null) {
            if ($request->filled('id')) {
                throw ValidationException::withMessages(['id' => 'مشتری با شماره عضویت '.$request->input('id').' یافت نشد.']);
            }
            $request->validate(['mobile' => 'required|iran_mobile']);
            $customer = Customer::create($request->only('mobile'));
            return ['newCreated' => true, 'customer' => $customer];
        }
        return ['customer' => $customer];
    }

    public function buy(Request $request, Customer $customer)
    {
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
        $customer->increment('point', intval($request->input('price', 0) / 1000));
        $coinPoint = 0;
        $pricePoint = intval($request->input('price')/1000);
        if ($pricePoint > 0) {
            foreach (setting('point.ranges') as $value) {
                if ($value['min'] <= $pricePoint && $value['max'] > $pricePoint) {
                    $coinPoint = $value['point'];
                }
            }
        }
        $coin = $coinPoint - intval($request->input('coin', 0));
        if ($coin != 0) {
            $customer->{ $coin > 0 ? 'increment' : 'decrement' }('coin', abs($coin));
        }
        debug($coinPoint);
        return ['customer' => $customer, 'message' => 'خرید با موفقیت انجام شد.'.($coinPoint > 0 ? "\nدست آورد سکه: $coinPoint" : '')];
    }

    public static function routes(): void
    {
        parent::routes();
        Route::prefix("modules/customers")->name("modules.customers.")->group(function () {
            Route::post('search', static::class.'@search')->name('search');
            Route::post('buy/{customer}', static::class.'@buy')->name('buy');
        });
    }
}
