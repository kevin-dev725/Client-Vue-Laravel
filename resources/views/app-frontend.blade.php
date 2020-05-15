<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>

    <link href="{{ mix('css/app.css', '/frontend') }}" rel="stylesheet">

    <script>
        window.App = {!! json_encode(\App\clientDomain::scriptVariables()) !!};
    </script>

@if(in_production())
    <!-- Facebook Pixel Code -->
        <script>
            !function (f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function () {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '2203435946356541');
        </script>
        <!-- End Facebook Pixel Code -->
    @if(auth()->check() && request()->has('after_signup'))
        <!-- Global site tag (gtag.js) - Google Ads: 720587630 -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=AW-720587630"></script>
            <script>
                window.dataLayer = window.dataLayer || [];

                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());
                gtag('config', 'AW-720587630');
            </script>
        @endif
    @endif

</head>

<body>
@if(in_production() && auth()->check() && request()->has('after_signup'))
    <!-- Event snippet for Registration conversion page -->
    <script>
        gtag('event', 'conversion', {'send_to': 'AW-720587630/ZadmCN3H8aYBEO6WzdcC'});
    </script>
@endif
<div id="app"></div>

<script src="https://js.stripe.com/v3/"></script>
<script src="{{ mix('js/manifest.js', '/frontend') }}"></script>
<script src="{{ mix('js/vendor.js', '/frontend') }}"></script>
<script src="{{ mix('js/app.js', '/frontend') }}"></script>
</body>
</html>
