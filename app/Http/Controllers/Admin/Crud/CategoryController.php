<?php

namespace App\Http\Controllers\Admin\Crud;

use Sanjab\Widgets\IdWidget;
use Sanjab\Widgets\TextWidget;
use Sanjab\Helpers\MaterialIcons;
use Sanjab\Helpers\CrudProperties;
use Sanjab\Controllers\CrudController;
use Illuminate\Database\Eloquent\Model;

class CategoryController extends CrudController
{
    protected static function properties(): CrudProperties
    {
        return CrudProperties::create('categories')
                ->model(\App\Category::class)
                ->title("دسته بندی")
                ->titles("دسته بندی ها")
                ->icon(MaterialIcons::VIEW_WEEK);
    }

    protected function init(string $type, Model $item = null): void
    {
        $this->widgets[] = IdWidget::create();

        $this->widgets[] = TextWidget::create('name', 'نام')
                            ->required();
    }
}
