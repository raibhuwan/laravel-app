<!DOCTYPE html>
<!-- Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 4Author: KeenThemesWebsite: http://www.keenthemes.com/Contact: support@keenthemes.comFollow: www.twitter.com/keenthemesDribbble: www.dribbble.com/keenthemesLike: www.facebook.com/keenthemesPurchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemesRenew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemesLicense: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.-->
<html lang="en">
<!-- begin::Head -->
<head>
    <meta charset="utf-8"/>
    <title>@yield('title', app_name())</title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="site_url" content="{{ config('app.url').'/'.config('backend.admin_backend_url') }}">
    <meta name="app_url" content="{{ config('app.url') }}">
    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>          WebFont.load({
            google: {"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]},
            active: function () {
                sessionStorage.fonts = true;
            }
        });
    </script>
    <!--end::Web font -->
    <!--begin::Page Vendors Styles -->
    @yield('before-styles')

    <link href="{{asset('assets/vendors/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/vendors/custom/fullcalendar/fullcalendar.bundle.css')}}" rel="stylesheet"
          type="text/css"/>
    <link href="{{asset('css/photoswipe.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('css/default-skin/default-skin.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('css/cropper.css')}}" rel="stylesheet" type="text/css"/>

    <!--end::Page Vendors Styles -->
    <!--begin::Base Styles -->
    <link href="{{asset('assets/vendors/base/vendors.bundle.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/demo/default/base/style.bundle.css')}}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ mix('/css/backend.css') }}">
    <!--end::Base Styles -->
    @yield('after-styles')
    <link rel="shortcut icon" href="{{ asset('assets/demo/default/media/img/logo/favicon.ico') }}"/>
</head>
<!-- end::Head -->
<!-- begin::Body -->
<body class="m-page--fluid m--skin- m-page--loading-enabled m-page--loading m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-aside-right--enabled m-footer--push m-aside--offcanvas-default">
<!--[html-partial:include:{"file":"partials\/_loader-base.html"}]/-->

@include('backend.includes.partials._loader-base')

<!--[html-partial:include:{"file":"_layout.html"}]/-->
@include('backend._layout')

<!--begin::Base Scripts -->
@yield('before-scripts')

<script src="{{ asset('assets/vendors/base/vendors.bundle.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/demo/default/base/scripts.bundle.js') }}" type="text/javascript"></script>

<!--end::Base Scripts -->
<!--begin::Page Vendors Scripts -->
<script src="{{ asset('assets/vendors/custom/fullcalendar/fullcalendar.bundle.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/demo/default/custom/crud/forms/widgets/bootstrap-datepicker.js') }}"
        type="text/javascript"></script>
<!--end::Page Vendors Scripts -->
<!--begin::Page Snippets -->
<script src="{{ asset('assets/app/js/dashboard.js') }}" type="text/javascript"></script>
<!--end::Page Snippets -->

<!--begin::Page Scripts -->
<script src="{{asset('assets/demo/default/custom/components/base/bootstrap-notify.js')}}"
        type="text/javascript"></script>
<!--end::Page Scripts -->


<!-- begin::Page Loader -->
<script>
    $(window).on('load', function () {
        $('body').removeClass('m-page--loading');
    });        </script>
<!-- end::Page Loader -->
@yield('after-scripts')

</body>
<!-- end::Body -->
</html>