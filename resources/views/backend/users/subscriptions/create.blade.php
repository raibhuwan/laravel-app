@extends('backend.users.layout')
@section('title', trans('strings.backend.plan_subscription.create_subscription'))
@section('subheader')
    {{ trans('strings.backend.plan_subscription.subscriptions') }}
@endsection
@section('users.layout')
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                        <span class="m-portlet__head-icon m--hide">
						<i class="la la-gear"></i>
						</span>
                <h3 class="m-portlet__head-text">
                    {{trans('strings.backend.plan_subscription.create_subscription')}}
                </h3>
            </div>
        </div>
    </div>
    <!--begin::Form-->
    {!! Form::open(['url' => route('user.subscription.store',$data['user_id']), 'class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed']) !!}
    @include('backend.users.subscriptions.partials.create_form')
    {!! Form::close() !!}
    <!--end::Form-->
@endsection
