<!doctype html>

<html class="no-js" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', app_name())</title>

    <!-- Meta -->
    <meta name="description" content="@yield('meta_description', 'Default Description')">
    <meta name="author" content="@yield('meta_author', 'Simon Shrestha')">
@yield('meta')

<!-- Styles -->
    @yield('before-styles')
    {{ Html::style(mix('css/backendLibrary.css')) }}
    {{ Html::style(mix('css/backend.css')) }}
    @yield('after-styles')

</head>

<body class="skin-{{ config('backend.theme') }} {{ config('backend.layout') }}">
{{--include('includes.partials.logged-in-as')--}}

<div class="wrapper">
@include('backend.includes.header')
@include('backend.includes.sidebar')
<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            @yield('page-header')
        </section>

        <section class="content">
            <div class="loader" style="display: none;">
                <div class="ajax-spinner ajax-skeleton"></div>
            </div><!--loader-->

            @include('includes.partials.messages')
            @yield('content')
        </section><!-- /.content -->

    </div><!-- /.content-wrapper -->
    @include('backend.includes.footer')
</div><!-- ./wrapper -->
<!-- JavaScripts -->
@yield('before-scripts')

{{ Html::script(mix('js/backend.js')) }}
@yield('after-scripts')
</body>
</html>