@extends('backend.users.layout')
@section('title',  trans('strings.backend.users.edit_user'))
@section('subheader')
    {{ trans('strings.backend.users.users') }}
@endsection
@section('users.layout')
    <!--begin::Form-->
    {!! Form::model($user , ['route' => ['user.update', $user->id], 'class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed' ,'method' => 'put']) !!}
    @include('backend.users.partials.form')
    {!! Form::close() !!}
    <!--end::Form-->
@endsection