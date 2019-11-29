<?php

namespace App\Http\Controllers\Admin\Setting;

use Sanjab\Controllers\SettingController;
use Sanjab\Helpers\SettingProperties;
use Sanjab\Widgets\File\UppyWidget;
use Sanjab\Widgets\TextWidget;

class HomeSettingController extends SettingController
{
    protected static function properties(): SettingProperties
    {
        return SettingProperties::create('home')
            ->title('تنظیمات صفحه اصلی');
    }

    protected function init(): void
    {
        $this->widgets[] = TextWidget::create('phone', 'شماره تماس')->required();

        $this->widgets[] = TextWidget::create('address', 'آدرس')->required();

        $this->widgets[] = UppyWidget::create('images', 'تصاویر')->width(1280)->height(960)->multiple()->required();
    }
}
