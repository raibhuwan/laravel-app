@extends('backend.modals.layout')

@section('modal-title', trans('strings.backend.users.delete_user'))

@section('modal-body')
    <input type="hidden" id="hidden_user_id">
    <input type="hidden" id="modal_token" value="{{ csrf_token() }}">
    <label class="m-radio m-radio--state-brand">
        <input type="radio" name="delete-option" value="SOFT" checked>
        <h6>
            {{ trans('strings.backend.users.delete.temporary_delete_modal_heading') }}
        </h6>
        <p>
            {{ trans('strings.backend.users.delete.user_soft_delete_message') }}
        </p>
        <span></span>
    </label>
    <br>
    <label class="m-radio m-radio--state-brand">
        <input type="radio" name="delete-option" value="HARD">
        <h6>
            {{ trans('strings.backend.users.delete.permanent_delete_modal_heading') }}
        </h6>
        <p>
            {{ trans('strings.backend.users.delete.user_hard_delete_message') }}
        </p>
        <span></span>
    </label>
@endsection()
@section('modal-footer')
    <button type="button" class="btn btn-danger" id="btnDeleteUser">{{ trans('strings.backend.users.delete.delete_btn') }}</button>
    <button type="button" class="btn btn-success" data-dismiss="modal">{{ trans('strings.backend.users.delete.close_btn') }}</button>
@endsection
