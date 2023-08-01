@extends('backend.modals.layout')

@section('modal-title', trans('strings.backend.plans.delete_plan'))

@section('modal-body')
    <input type="hidden" id="hidden_plan_id">
    <input type="hidden" id="modal_token" value="{{ csrf_token() }}">
    <label class="m-radio m-radio--state-brand">
        <h6>
            {{trans('strings.backend.plans.delete.are_you_sure_to_delete_this_plan')}}
        </h6>
    </label>
@endsection()
@section('modal-footer')
    <button type="button" class="btn btn-danger"
            id="btnDeletePlan">{{ trans('strings.backend.plans.delete.delete_btn') }}</button>
    <button type="button" class="btn btn-success"
            data-dismiss="modal">{{ trans('strings.backend.plans.delete.close_btn') }}</button>
@endsection
