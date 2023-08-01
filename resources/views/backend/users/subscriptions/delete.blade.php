@extends('backend.modals.layout')

@section('modal-title', trans('strings.backend.plan_subscription.delete_subscription'))

@section('modal-body')
    <input type="hidden" id="hidden_subscription_id">
    <input type="hidden" id="modal_token" value="{{ csrf_token() }}">
    <label class="m-radio m-radio--state-brand">
        <h6>
            {{trans('strings.backend.plan_subscription.delete.are_you_sure_to_delete_this_subscription')}}
        </h6>
    </label>
@endsection()

@section('modal-footer')
    <button type="button" class="btn btn-danger"
            id="btnDeleteSubscription">{{ trans('strings.backend.plan_subscription.delete.delete_btn') }}</button>
    <button type="button" class="btn btn-success"
            data-dismiss="modal">{{ trans('strings.backend.plan_subscription.delete.close_btn') }}</button>
@endsection
