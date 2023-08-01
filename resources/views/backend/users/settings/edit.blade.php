@extends('backend.users.layout')
@section('title', trans('strings.backend.settings.edit.edit_setting'))
@section('subheader')
    {{ trans('strings.backend.images.image') }}
@endsection

@section('users.layout')
    {!! Form::model($setting , ['route' => ['setting.update', $userDetails->user_detail_id], 'class' => 'm-form m-form--fit m-form--label-align-right' ,'method' => 'put']) !!}
    <div class="m-portlet__body">
        <div class="form-group m-form__group row">
            {!! Form::label('m_slider_1', trans('strings.backend.settings.edit.search_distance'), ['class' => 'col-form-label col-lg-3 col-sm-12']) !!}
            <div class="col-lg-8 col-md-9 col-sm-12 {!! $errors->has('search_distance') ?  'has-danger' : ''!!}">
                <div class="m-ion-range-slider">
                    {!! Form::hidden('search_distance', isset($setting->search_distance) ? $setting->search_distance: config('default.setting.search_distance'), ['id' => 'm_slider_1', 'data-min' => '1', 'data-max' => '100']) !!}
                </div>
                <span class="m-form__help">{{trans('strings.backend.settings.edit.the_distance_is_in_mile')}}</span>
                @if($errors->has('search_distance'))
                    <div class="form-control-feedback">{{ $errors->first('search_distance') }}</div>
                @endif
            </div>
        </div>
        <div class="m-form__group form-group row">
            {!! Form::label('', trans('strings.backend.settings.edit.interested_in'), ['class' => 'col-form-label col-lg-3 col-sm-12']) !!}
            <div class="col-9 {!! $errors->has('interested_in') ?  'has-danger' : ''!!}">
                <div class="m-radio-list">
                    <label class="m-radio">
                        {!! Form::radio('interested_in', 'FRIENDSHIP', (isset($setting->interested_in) && $setting->interested_in == 'FRIENDSHIP') ? true : ((config('default.setting.interested_in') == 'FRIENDSHIP') ? true : '')) !!}
                        {{ trans('strings.backend.settings.edit.friendship') }}
                        <span></span>
                    </label>
                    <label class="m-radio">
                        {!! Form::radio('interested_in', 'RELATIONSHIP',  (isset($setting->interested_in) && $setting->interested_in == 'RELATIONSHIP') ? true : ((config('default.setting.interested_in') == 'RELATIONSHIP') ? true : '')) !!}
                        {{trans('strings.backend.settings.edit.relationship')}}
                        <span></span>
                    </label>
                    <label class="m-radio">
                        {!! Form::radio('interested_in', 'CASUAL_MEETUP', (isset($setting->interested_in) && $setting->interested_in == 'CASUAL_MEETUP') ? true :((config('default.setting.interested_in') == 'CASUAL_MEETUP') ? true : '')) !!}
                        {{trans('strings.backend.settings.edit.casual_meetup')}}
                        <span></span>
                    </label>
                </div>
                @if($errors->has('interested_in'))
                    <div class="form-control-feedback">{{ $errors->first('interested_in') }}</div>
                @endif
            </div>
        </div>
        <div class="m-form__group form-group row">
            {!! Form::label('', trans('strings.backend.settings.edit.you_want_to_date_with'), ['class' => 'col-form-label col-lg-3 col-sm-12']) !!}
            <div class="col-9 {!! $errors->has('date_with') ?  'has-danger' : ''!!}">
                <div class="m-radio-list">
                    @if($userDetails->user_detail_gender == 'MALE')
                        <label class="m-radio">
                            {!! Form::radio('date_with', 'FEMALE', true).' '. trans('strings.backend.settings.edit.female') !!}
                            <span></span>
                        </label>
                        <label class="m-radio">
                            {!! Form::radio('date_with', 'MALE') .' '. trans('strings.backend.settings.edit.male')!!}
                            <span></span>
                        </label>
                        <label class="m-radio">
                            {!! Form::radio('date_with', 'BOTH') .' '. trans('strings.backend.settings.edit.both')!!}
                            <span></span>
                        </label>
                    @else
                        <label class="m-radio">
                            {!! Form::radio('date_with', 'FEMALE') .' '. trans('strings.backend.settings.edit.female')!!}
                            <span></span>
                        </label>
                        <label class="m-radio">
                            {!! Form::radio('date_with', 'MALE', true) .' '. trans('strings.backend.settings.edit.male')!!}
                            <span></span>
                        </label>
                        <label class="m-radio">
                            {!! Form::radio('date_with', 'BOTH') .' '. trans('strings.backend.settings.edit.both')!!}
                            <span></span>
                        </label>
                    @endif

                </div>
                @if($errors->has('date_with'))
                    <div class="form-control-feedback">{{ $errors->first('date_with') }}</div>
                @endif
            </div>
        </div>
        <div class="form-group m-form__group row">
            {!! Form::label('m_slider_4', trans('strings.backend.settings.edit.show_ages'), ['class' => 'col-form-label col-lg-3 col-sm-12']) !!}
            <div class="col-lg-8 col-md-9 col-sm-12 {!! $errors->has('show_ages') ?  'has-danger' : ''!!}">
                <div class="m-ion-range-slider">
                    {!! Form::hidden('show_ages', null, ['id' => 'm_slider_4', 'data-min' => '18', 'data-max' => '55', 'data-from' => isset($setting->show_ages_min) ?  $setting->show_ages_min : config('default.setting.show_ages_min'), 'data-to' => isset($setting->show_ages_max) ? $setting->show_ages_max : config('default.setting.show_ages_max') ]) !!}
                </div>
                @if($errors->has('show_ages'))
                    <div class="form-control-feedback">{{ $errors->first('show_ages') }}</div>
                @endif
            </div>
        </div>
        <div class="m-form__group form-group row">
            {!! Form::label('', trans('strings.backend.settings.edit.show_my_distance'), ['class' => 'col-form-label col-lg-3 col-sm-12']) !!}
            <div class="col-lg-8 col-md-9 col-sm-12  {!! $errors->has('privacy_show_distance') ?  'has-danger' : ''!!}">
											<span class="m-switch m-switch--outline m-switch--danger">
												<label>
                                                     {!! Form::checkbox('privacy_show_distance', isset($setting->privacy_show_distance) ? $setting->privacy_show_distance : 1, (isset($setting->privacy_show_distance) && $setting->privacy_show_distance == 0   ? false : true)) !!}
						                        <span></span>
						                        </label>
						                    </span>
                @if($errors->has('privacy_show_distance'))
                    <div class="form-control-feedback">{{ $errors->first('privacy_show_distance') }}</div>
                @endif
            </div>
        </div>
        <div class="m-form__group form-group row">
            {!! Form::label('', trans('strings.backend.settings.edit.show_my_age'), ['class' => 'col-form-label col-lg-3 col-sm-12']) !!}
            <div class="col-lg-8 col-md-9 col-sm-12  {!! $errors->has('privacy_show_age') ?  'has-danger' : ''!!}">
											<span class="m-switch m-switch--outline m-switch--danger">
												<label>
                                                    {!! Form::checkbox('privacy_show_age', isset($setting->privacy_show_age) ? $setting->privacy_show_age : 1, (isset($setting->privacy_show_age) && $setting->privacy_show_age == 0   ? false : true)) !!}
						                        <span></span>
						                        </label>
						                    </span>
                @if($errors->has('privacy_show_age'))
                    <div class="form-control-feedback">{{ $errors->first('privacy_show_age') }}</div>
                @endif
            </div>
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
    <script src="{{ asset('assets/demo/default/custom/crud/forms/widgets/ion-range-slider.js') }}"
            type="text/javascript"></script>
@endsection
