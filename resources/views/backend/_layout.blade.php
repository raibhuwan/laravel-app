<!-- begin:: Page -->
<div class="m-grid m-grid--hor m-grid--root m-page">
    <!--[html-partial:include:{"file":"partials\/_header-base.html"}]/-->
@include('backend.includes.partials._header-base')
<!-- begin::Body -->
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
        <!--[html-partial:include:{"file":"partials\/_aside-left.html"}]/-->
        @include('backend.includes.partials._aside-left')
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <!--[html-partial:include:{"file":"partials\/_subheader-default.html"}]/-->
            @include('backend.includes.partials._subheader-default')
            <div class="m-content">
                @if (session('status'))
                    <div class="alert alert-brand alert-dismissible fade show   m-alert m-alert--square m-alert--air"
                         role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        </button>
                        {{ session('status') }}
                    </div>
                @endif
                @if (session('fail'))
                    <div class="alert alert-danger alert-dismissible fade show   m-alert m-alert--square m-alert--air"
                         role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        </button>
                        {{ session('fail') }}
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
        <!--[html-partial:include:{"file":"partials\/_aside-right.html"}]/-->
        {{--@include('backend.includes.partials._aside-right')--}}
    </div>
    <!-- end:: Body -->
    <!--[html-partial:include:{"file":"partials\/_footer-default.html"}]/-->
    @include('backend.includes.partials._footer-default')
</div>
<!-- end:: Page -->
<!--[html-partial:include:{"file":"partials\/_layout-quick-sidebar.html"}]/-->
@include('backend.includes.partials._layout-quick-sidebar')

<!--[html-partial:include:{"file":"partials\/_layout-scroll-top.html"}]/-->
@include('backend.includes.partials._layout-scroll-top')

<!--[html-partial:include:{"file":"partials\/_layout-tooltips.html"}]/-->

