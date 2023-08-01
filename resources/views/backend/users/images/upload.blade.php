@extends('backend.modals.layout')

@section('modal-title', 'Upload Image')

@section('modal-body')
    <div class="m-form m-form--fit m-form--label-align-right">
        <div class="m-portlet__body">
            <div class="form-group m-form__group row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    {{--<form class="m-dropzone dropzone" action="{{ route('image.store', 123) }}" id="my-dropzone-container">--}}
                    {{ Form::open(['url' => route('image.store',123), 'class' => 'm-dropzone dropzone', 'id' => 'my-dropzone-container']) }}
                    <div class="m-dropzone__msg dz-message needsclick">
                        <h3 class="m-dropzone__msg-title">Drop files here or click to upload or change.</h3>
                        <span class="m-dropzone__msg-desc">Please select image</span>
                    </div>
                    {{--</form>--}}
                    {{ Form::close() }}
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <img  class ="test"  id = "image" src="{{ $image[0]['path'] }}"
                          alt="Image description"
                          data-med-size="1024x1024"/>
                </div>
            </div>

        </div>
    </div>
    <div >
        {{--<img id="image" src="{{ $image[0]['path'] }}"--}}
             {{--alt="Image description" height="auto" width="100%"--}}
             {{--data-med-size="1024x1024"/>--}}
    </div>
@endsection()
@section('modal-footer')
    <button type="button" class="btn btn-danger" id="btnDeleteUser">Upload</button>
    <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
@endsection

