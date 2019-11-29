<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>


    <style>
        :root {
            --first-background-image: url(@json(Storage::disk('public')->url(array_first(setting('home.images')))));
        }
    </style>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body style="height: 100% !important">
    <div id="animated-bg">
        <h1>{{ config('app.name') }}</h1>
        <div class="container h-100">
            <div class="row h-100 justify-content-center align-items-center">
                <form id="point-form" class="col-12">
                    <div class="row">
                        <div class="col-lg-5 col-12 form-group">
                            <input type="number" class="form-control" id="code" placeholder="کد عضویت">
                        </div>
                        <div class="col-lg-2 col-12 text-center">
                            <span>یا</span>
                        </div>
                        <div class="col-lg-5 col-12 form-group">
                            <input type="tel" class="form-control" id="mobile" placeholder="شماره همراه">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-block btn-outline-secondary">بررسی</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <footer>
            <span class="address">آدرس: {{ setting('home.address') }}</span>
            <span class="phone">شماره تماس: <a href="tel:{{ setting('home.phone') }}">{{ setting('home.phone') }}</a></span>
        </footer>
    </div>

    <script>
        @if(setting('home.images'))
            window.backgroundImages = @json(array_map(function ($im) {return Storage::disk('public')->url($im);}, setting('home.images')))
        @endif
    </script>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
