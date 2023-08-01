@extends('backend.users.layout')
@section('title', trans('strings.backend.plan_subscription.subscriptions'))
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
                    {{trans('strings.backend.plan_subscription.edit_subscription')}}
                </h3>
            </div>
        </div>
    </div>

    <!--begin::Form-->
    {!! Form::model($data['plan_subscription'] , ['route' => ['user.subscription.update',$data['user_id'], $data['plan_subscription']->id], 'class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed' ,'method' => 'put']) !!}
    @include('backend.users.subscriptions.partials.edit_form')
    {!! Form::close() !!}

    <!--end::Form-->
@endsection
