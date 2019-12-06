<?php

namespace App\Http\Controllers\Admin\Crud;

use Sanjab\Widgets\IdWidget;
use Sanjab\Widgets\TextWidget;
use Sanjab\Widgets\MoneyWidget;
use Sanjab\Helpers\MaterialIcons;
use Sanjab\Helpers\CrudProperties;
use Sanjab\Controllers\CrudController;
use Illuminate\Database\Eloquent\Model;
use Sanjab\Widgets\Relation\BelongsToPickerWidget;

class ProductController extends CrudController
{
    protected static function properties(): CrudProperties
    {
        return CrudProperties::create('products')
                ->model(\App\Product::class)
                ->title("محصول")
                ->titles("محصولات")
                ->icon(MaterialIcons::GRAPHIC_EQ);
    }

    protected function init(string $type, Model $item = null): void
    {
        $this->widgets[] = IdWidget::create();

        $this->widgets[] = BelongsToPickerWidget::create('category', 'دسته بندی')
                            ->format('%id. %name')
                            ->required();

        $this->widgets[] = TextWidget::create('name', 'نام')
                            ->required();

        $this->widgets[] = MoneyWidget::create('price', 'قیمت')
                            ->required()
                            ->postfix(' تومان')
                            ->min(1000)
                            ->precision(0);
    }
}
