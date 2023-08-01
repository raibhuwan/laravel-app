@extends('backend.index')
@section('title', 'Users')
@section('subheader')
    {{ trans('menus.backend.aside-left.users.reported_users') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="m-portlet">
                <div class="m-portlet__body">
                    <div class="m-card-profile">
                        <div class="m-card-profile__title">
                            {{ trans('backend/reports.users.reported_by') }}
                        </div>
                        <div class="m-card-profile__pic">
                            <div class="m-card-profile__pic-wrapper">
                                <img src="{{   HelperFunctions::getImageLink($userReportedBy->reported_by_image_link, $userReportedBy->reported_by_image_path,  $userReportedBy->reported_by_image_name, $userReportedBy->reported_by_gender)  }}"
                                     alt=""/>
                            </div>
                        </div>
                        <div class="m-card-profile__details">
                            <span class="m-card-profile__name">{{ $userReportedBy->reported_by_name }}</span>
                            <a href="" class="m-card-profile__email m-link">{{ $userReportedBy->reported_by_email }}</a>
                            <a href="{{route('user.edit', [$userReportedBy->reported_by_id])}}"
                               class="m-card-profile__profile m-link"> {{ trans('backend/reports.users.view_profile') }}</a>
                        </div>
                    </div>
                    <div class="m-portlet__body-separator"></div>
                    <div class="m-widget1 m-widget1--paddingless">
                        <div class="m-widget1__item">
                            <div class="row m-row--no-padding align-items-center">
                                <div class="col">
                                    <h4 class="m-widget1__title">{{ trans('backend/reports.users.gender') }}</h4>
                                    <span class="m-widget1__desc">{{$userReportedBy->reported_by_gender}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="m-widget1__item">
                            <div class="row m-row--no-padding align-items-center">
                                <div class="col">
                                    <h3 class="m-widget1__title">{{ trans('backend/reports.users.phone') }}</h3>
                                    <span class="m-widget1__desc">{{ $userReportedBy->reported_by_country_code.$userReportedBy->reported_by_phone}}</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="m-portlet__body-separator"></div>

                    <div class="m-widget1 m-widget1--paddingless">
                        <div class="m-widget1__item">
                            <div class="row m-row--no-padding align-items-center">
                                <div class="col">
                                    <h3 class="m-widget1__title">{{ trans('backend/reports.users.report.report') }}</h3>
                                    <span class="m-widget1__desc">{{ trans('backend/reports.users.report.number_of_times_this_user_has_reported') }}</span>
                                </div>
                                <div class="col m--align-right">
                                    <span class="m-widget1__number m--font-brand">{{$userReportedBy->number_of_times}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="m-portlet">

                <div class="m-portlet__body">
                    <div class="m-card-profile">
                        <div class="m-card-profile__title">
                            {{ trans('backend/reports.users.reported_to') }}
                        </div>
                        <div class="m-card-profile__pic">
                            <div class="m-card-profile__pic-wrapper">
                                <img src="{{  HelperFunctions::getImageLink($userReportedTo->reported_to_image_link, $userReportedTo->reported_to_image_path,  $userReportedTo->reported_to_image_name, $userReportedTo->reported_to_gender) }}"
                                     alt=""/>
                            </div>
                        </div>
                        <div class="m-card-profile__details">
                            <span class="m-card-profile__name">{{ $userReportedTo->reported_to_name }}</span>
                            <a href="" class="m-card-profile__email m-link">{{ $userReportedTo->reported_to_email }}</a>
                            <a href="{{route('user.edit', [$userReportedTo->reported_to_id])}}"
                               class="m-card-profile__profile m-link">{{ trans('backend/reports.users.view_profile') }}</a>
                        </div>
                    </div>
                    <div class="m-portlet__body-separator"></div>
                    <div class="m-widget1 m-widget1--paddingless">
                        <div class="m-widget1__item">
                            <div class="row m-row--no-padding align-items-center">
                                <div class="col">
                                    <h4 class="m-widget1__title">{{ trans('backend/reports.users.gender') }}</h4>
                                    <span class="m-widget1__desc">{{$userReportedTo->reported_to_gender}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="m-widget1__item">
                            <div class="row m-row--no-padding align-items-center">
                                <div class="col">
                                    <h3 class="m-widget1__title">{{ trans('backend/reports.users.phone') }}</h3>
                                    <span class="m-widget1__desc">{{ $userReportedTo->reported_to_country_code.$userReportedTo->reported_to_phone}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="m-widget1__item">
                            <div class="row m-row--no-padding align-items-center">
                                <div class="col">
                                    <h3 class="m-widget1__title m--font-danger">{{ trans('backend/reports.users.reason') }}</h3>
                                    <span class="m-widget1__desc">{{ $userReportedTo->reported_to_reason}}</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="m-portlet__body-separator"></div>

                    <div class="m-widget1 m-widget1--paddingless">
                        <div class="m-widget1__item">
                            <div class="row m-row--no-padding align-items-center">
                                <div class="col">
                                    <h3 class="m-widget1__title">{{ trans('backend/reports.users.report.report') }}</h3>
                                    <span class="m-widget1__desc">{{ trans('backend/reports.users.report.number_of_times_reported_by_users') }}</span>
                                </div>
                                <div class="col m--align-right">
                                    <span class="m-widget1__number m--font-danger">{{$userReportedTo->number_of_times}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::open(['route' => ['report.user.update', $userReportedTo->reported_id], 'class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed','method' => 'put']) !!}

                <div class="m-portlet__body">
                    <div class="m-form__group form-group {!! $errors->has('reportUserAction') ?  'has-danger' : ''!!}">
                        <h5 class="m-widget1__title">{{ trans('backend/reports.users.actions') }}</h5>
                        <div class="m-radio-list ">
                            <label class="m-radio m-radio--state-danger">
                                {!! Form::radio('action', 1 ) !!} {{ trans('backend/reports.users.deactivate') }}
                                <span></span>
                            </label>
                            <label class="m-radio m-radio--state-success">
                                {!! Form::radio('action', 2 ,true) !!} {{ trans('backend/reports.users.ignore') }}
                                <span></span>
                            </label>
                        </div>
                        @if($errors->has('reportUserAction'))
                            <div class="form-control-feedback">{{ $errors->first('reportUserAction') }}</div>
                        @endif
                    </div>

                </div>

                <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                    <div class="m-form__actions m-form__actions--solid">
                        <div class="row">
                            <div class="col-lg-6">
                                <button type="submit"
                                        class="btn btn-primary">{{ trans('backend/reports.users.submit') }}</button>
                                <button type="reset"
                                        class="btn btn-secondary">{{ trans('backend/reports.users.cancel') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

