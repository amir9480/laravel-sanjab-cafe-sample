<?php

namespace App\Http\Controllers\Admin\Setting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Sanjab\Controllers\SettingController;
use Sanjab\Helpers\SettingProperties;
use Sanjab\Widgets\ItemListWidget;
use Sanjab\Widgets\NumberWidget;
use Sanjab\Widgets\TextWidget;

class PointSettingController extends SettingController
{
    protected static function properties(): SettingProperties
    {
        return SettingProperties::create('point')
            ->title('تنظیمات امتیاز');
    }

    protected function init(): void
    {
        $this->widgets[] = ItemListWidget::create('ranges', 'جدول امتیاز')
                            ->addWidget(NumberWidget::create('min', 'حداقل')->required()->cols(4)->min(0))
                            ->addWidget(NumberWidget::create('max', 'حداکثر')->required()->cols(4)->min(0))
                            ->addWidget(NumberWidget::create('point', 'سکه')->required()->cols(4))
                            ->required();
    }

    /**
     * Validation after callback.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @param \Illuminate\Http\Request $request
     * @param string $type  create|edit
     * @param Model|null  $item
     * @return array
     */
    public function validationAfter(Validator $validator, Request $request, string $type, Model $item = null)
    {
        if (is_array($request->ranges)) {
            foreach ($request->ranges as $key => $range) {
                if (is_numeric($request->input("ranges.$key.min")) && is_numeric($request->input("ranges.$key.max"))) {
                    foreach ($request->ranges as $key2 => $range2) {
                        if ($key != $key2 && is_numeric($request->input("ranges.$key2.min")) && is_numeric($request->input("ranges.$key2.max"))) {
                            if (!($range['min'] >= $range2['max'] || $range['max'] <= $range2['min'])) {
                                $validator->errors()->add("ranges.$key.min", 'تداخل با '.$request->input("ranges.$key2.point"));
                                $validator->errors()->add("ranges.$key.max", 'تداخل با '.$request->input("ranges.$key2.point"));
                            }
                            if ($range['min'] >= $range['max']) {
                                $validator->errors()->add("ranges.$key.min", 'حداقل امتیاز نباید مساوی یا بزرگتر از حداکثر امتیاز باشد');
                            }
                        }
                    }
                }
            }
        }
    }
}
