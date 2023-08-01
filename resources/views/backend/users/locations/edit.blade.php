@extends('backend.users.layout')
@section('title', trans('strings.backend.locations.edit.edit_location'))
@section('subheader')
    {{ trans('strings.backend.locations.location') }}
@endsection

@section('users.layout')
    {!! Form::model($location , ['route' => ['location.update', $userDetails->user_detail_id], 'class' => 'm-form m-form--label-align-right' ,'method' => 'put']) !!}
    <div class="m-portlet__body">
        <div class="input-group m--margin-bottom-20 ">
            <input type="text" class="form-control" id="m_gmap_8_address" placeholder="address...">
            <div class="input-group-append">
                <button class="btn btn-primary" id="m_gmap_8_btn"><i class="fa fa-search"></i></button>
            </div>
        </div>

        {!! Form::hidden('latitude', isset($location->latitude) ? $location->latitude : '' , ['id' => 'userInputLat']) !!}
        {!! Form::hidden('longitude', isset($location->longitude) ? $location->longitude : '', ['id' => 'userInputLong']) !!}

        <div id="m_gmap_8" style="height:300px;">
        </div>

        <div class="form-group m-form__group  {!! $errors->has('latitude')  || $errors->has('longitude') ?  'has-danger' : ''!!}  ">
            @if($errors->has('latitude') || $errors->has('longitude'))
                <div class="form-control-feedback">{{trans('strings.backend.locations.validation_message.please_select_marker_on_the_map')}}</div>
            @endif

        </div>
    </div>
    <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
        <div class="m-form__actions m-form__actions--solid">
            <div class="row">
                <div class="col-lg-6">
                    <input class="btn btn-primary" type="submit"
                           value="{{trans('strings.backend.settings.edit.submit_btn')}}">
                    <button type="reset"
                            class="btn btn-secondary">{{trans('strings.backend.settings.edit.cancel_btn')}}</button>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('after-scripts')
    <script src="{{ asset('js/google-maps-cutom.js') }}"
            type="text/javascript"></script>
    <script src="https://maps.google.com/maps/api/js?key={{config('backend.google_map_api_key')}}&libraries=places&callback=initMap"
            type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/custom/gmaps/gmaps.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/demo/default/custom/components/maps/google-maps.js') }}"
            type="text/javascript"></script>

@endsection