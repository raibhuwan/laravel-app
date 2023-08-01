@extends('backend.index')
@section('title',  trans('strings.backend.plans.edit_plan'))
@section('subheader')
    {{trans('strings.backend.plans.plans')}}
@endsection
@section('content')
    {{--{{$errors}}--}}
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                        <span class="m-portlet__head-icon m--hide">
						<i class="la la-gear"></i>
						</span>
                            <h3 class="m-portlet__head-text">
                                {{trans('strings.backend.plans.edit_plan')}}
                            </h3>
                        </div>
                    </div>
                </div>
                <!--begin::Form-->
            {!! Form::model($data['plan'] , ['route' => ['plan.update', $data['plan']->id], 'class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed' ,'method' => 'put']) !!}
            @include('backend.plans.partials.form')
            {!! Form::close() !!}

            <!--end::Form-->
            </div>
            <!--end::Portlet-->
        </div>
    </div>

@endsection