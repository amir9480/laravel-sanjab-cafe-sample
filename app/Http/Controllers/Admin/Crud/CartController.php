<?php

namespace App\Http\Controllers\Admin\Crud;

use App\Cart;
use Sanjab\Helpers\Action;
use Sanjab\Cards\StatsCard;
use Sanjab\Widgets\IdWidget;
use Sanjab\Widgets\TextWidget;
use Sanjab\Helpers\MaterialIcons;
use Sanjab\Helpers\CrudProperties;
use Sanjab\Widgets\CheckboxWidget;
use Illuminate\Support\Facades\Route;
use Sanjab\Controllers\CrudController;
use Illuminate\Database\Eloquent\Model;
use Sanjab\Widgets\Relation\BelongsToPickerWidget;

class CartController extends CrudController
{
    protected static function properties(): CrudProperties
    {
        return CrudProperties::create('carts')
                ->model(\App\Cart::class)
                ->title("سفارش")
                ->titles("سفارشات")
                ->icon(MaterialIcons::SHOPPING_CART)
                ->creatable(false)
                ->editable(false)
                ->badge(function () {
                    return Cart::where('seen', false)->count();
                })
                ->defaultOrder('seen')
                ->defaultOrderDirection('asc');
    }

    protected function init(string $type, Model $item = null): void
    {
        $this->cards[] = StatsCard::create('سفارشات بررسی نشده')
                            ->value(function () {
                                return Cart::where('seen', false)->count();
                            });

        array_unshift(
            $this->actions,
            Action::create('لیست سفارش')
                ->tag('cart-products')
                ->perItem(true)
                ->authorize(function ($item) {
                    return true;
                })
                ->icon(MaterialIcons::LIST)
                ->modalSize("lg")
                ->variant("primary")
        );

        $this->widgets[] = IdWidget::create();

        $this->widgets[] = CheckboxWidget::create('seen', 'بررسی شده')
                            ->fastChange(true);

        $this->widgets[] = TextWidget::create('date', 'زمان')
                            ->customModifyResponse(function ($response, $item) {
                                $response->date = $item->date_fa_ft;
                            });

        $this->widgets[] = BelongsToPickerWidget::create('customer', 'مشتری')
                            ->format('%id - %mobile');
    }

    /**
     * Get products inside cart.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function cartProducts(Cart $cart)
    {
        return $cart->products;
    }

    public static function routes(): void
    {
        parent::routes();
        Route::prefix("modules")->name("modules.")->group(function () {
            Route::get(static::property('route').'/cart-products/{cart}', static::class.'@cartProducts')->name(static::property('route').'.cart-products');
        });
    }
}
