@extends('backend.modals.layout')

@section('modal-title', trans('strings.backend.images.delete.delete_image'))

@section('modal-body')
    <input type="hidden" id="modal_token" value="{{ csrf_token() }}">
    <input type="hidden" id="hidden_image_id">
    <h6>
        {{ trans('strings.backend.images.delete.are_you_sure_you_want_to_delete_this_image') }}
    </h6>
    <p>
        {{ trans('strings.backend.images.delete.this_will_delete_image_number') }}
        <span class="image-number-modal"></span>
    </p>

@endsection()
@section('modal-footer')
    <button type="button" class="btn btn-danger"
            id="btnDeleteImage">{{ trans('strings.backend.images.delete.delete_btn') }}</button>
    <button type="button" class="btn btn-success"
            data-dismiss="modal">{{ trans('strings.backend.images.delete.close_btn') }}</button>
@endsection
