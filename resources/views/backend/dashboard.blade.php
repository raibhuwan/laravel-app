@extends('backend.index')
@section('title',  trans('strings.backend.dashboard.dashboard'))
@section('subheader')
    {{trans('strings.backend.dashboard.dashboard')}}
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-6">
            <!--begin:: Widgets/Support Tickets -->
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                {{trans('strings.backend.dashboard.new_users')}}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-widget3">
                        @foreach ($users as $user)
                            <div class="m-widget3__item">
                                <div class="m-widget3__header">
                                    <div class="m-widget3__user-img">
                                        <img class="m-widget3__img"
                                             src="{{  HelperFunctions::getImageLink($user->image_link, $user->image_path,  $user->image_name, $user->user_gender) }}"
                                             alt="">
                                    </div>
                                    <div class="m-widget3__info">
                        <span class="m-widget3__username">
                        <a href="{{ config('app.url').'/'.config('backend.admin_backend_url') }}/user/{{$user->user_id}}/edit/">{{$user->user_name}}</a>
                        </span><br>
                                        <span class="m-widget3__time">
                        @php
                            $created = new \Carbon\Carbon($user->user_created_at);
                            $now = \Carbon\Carbon::now();
                            $difference = ($created->diff($now)->days < 1) ? 'Today' : $created->diffForHumans($now);
                        @endphp
                                            {{$difference}}
                        </span>
                                    </div>
                                    <span class="m-widget3__status m--font-info">
                     {{$user->user_gender}}
                     </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!--end:: Widgets/Support Tickets -->
        </div>
        <div class="col-xl-4">
            <!--begin:: Widgets/Activity-->
            <div class="m-portlet m-portlet--bordered-semi m-portlet--widget-fit m-portlet--skin-light  m-portlet--rounded-force">
                <div class="m-portlet__head">
                </div>
                <div class="m-portlet__body">
                    <div class="m-widget17">
                        <div class="m-widget17__stats">
                            <div class="m-widget17__items m-widget17__items-col1">
                                <div class="m-widget17__item">
                        <span class="m-widget17__icon">
                        <i class="flaticon-users m--font-brand"></i>
                        </span>
                                    <span class="m-widget17__subtitle">
                        {{trans('strings.backend.dashboard.users')}}
                        </span>
                                    <span class="m-widget17__desc">
                        {{$totalUsers}} {{trans('strings.backend.dashboard.users')}}
                        </span>
                                </div>
                                <div class="m-widget17__item">
                        <span class="m-widget17__icon">
                        <i class="flaticon-paper-plane m--font-info"></i>
                        </span>
                                    <span class="m-widget17__subtitle">
                        {{trans('strings.backend.dashboard.reports')}}
                        </span>
                                    <span class="m-widget17__desc">
                        {{$totalReportedUsers}} {{trans('strings.backend.dashboard.profiles_reported')}}
                        </span>
                                </div>
                            </div>
                            <div class="m-widget17__items m-widget17__items-col2">
                                <div class="m-widget17__item">
                        <span class="m-widget17__icon">
                        <i class="flaticon-pie-chart m--font-success"></i>
                        </span>
                                    <span class="m-widget17__subtitle">
                        {{trans('strings.backend.dashboard.matches')}}
                        </span>
                                    <span class="m-widget17__desc">
                        {{$totalMatches}} {{trans('strings.backend.dashboard.matches')}}
                        </span>
                                </div>
                                <div class="m-widget17__item">
                        <span class="m-widget17__icon">
                        <i class="flaticon-time m--font-danger"></i>
                        </span>
                                    <span class="m-widget17__subtitle">
                         {{trans('strings.backend.dashboard.jobs')}}
                        </span>
                                    <span class="m-widget17__desc">
                        {{$totalFailedJobs}} {{trans('strings.backend.dashboard.failed_jobs')}}
                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end:: Widgets/Activity-->
        </div>
    </div>
@endsection
@section('after-scripts')
@endsection