<div class="m-portlet__body">
    <div class="form-group m-form__group row ">
        <div class="col-lg-6 {!! $errors->has('name') ?  'has-danger' : ''!!}">
            {!! Form::label('userInputName', trans('strings.backend.users.create.name.full_name')) !!}
            {!! Form::text('name', null, ['id' => 'userInputName', 'class' => 'form-control m-input' , 'placeholder' => trans("strings.backend.users.create.name.enter_full_name") ]) !!}
            @if($errors->has('name'))
                <div class="form-control-feedback">{{ $errors->first('name') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
        <div class="col-lg-6">

            <div class="row">
                <div class="col-lg-5 {!! $errors->has('country_code') ?  'has-danger' : ''!!} ">
                    {!! Form::label('userInputCountryCode', trans('strings.backend.users.create.phone.country_code')) !!}
                    {!! Form::select('country_code', $select,null, ['id' => 'userInputCountryCode', 'class' => 'form-control m-input']) !!}
                    @if($errors->has('country_code'))
                        <div class="form-control-feedback">{{ $errors->first('country_code') }}</div>
                    @endif
                    <span class="m-form__help"></span>
                </div>
                <div class="col-lg-7  {!! $errors->has('phone') ?  'has-danger' : ''!!} ">
                    {!! Form::label('userInputPhone', trans('strings.backend.users.create.phone.phone_number')) !!}
                    {!! Form::text('phone', null, ['id' => 'userInputPhone', 'class' => 'form-control m-input' , 'placeholder' => trans("strings.backend.users.create.phone.enter_phone_number") ]) !!}
                    @if($errors->has('phone'))
                        <div class="form-control-feedback">{{ $errors->first('phone') }}</div>
                    @endif
                    <span class="m-form__help"></span>
                </div>

            </div>

            {{--{!! Form::label('userInputPhone', trans('strings.backend.users.create.phone.phone_number')) !!} {!! Form::text('userInputPhone', '', ['id' => 'userInputPhone', 'class' => 'form-control m-input' , 'placeholder' => trans("strings.backend.users.create.phone.enter_phone_number") ]) !!}--}}
        </div>
    </div>

    <div class="form-group m-form__group row">
        <div class="col-lg-6 {!! $errors->has('password') ?  'has-danger' : ''!!} ">
            {!! Form::label('userInputPassword', trans('strings.backend.users.create.password.password')) !!}
            {!! Form::password('password', ['id' => 'userInputPassword', 'class' => 'form-control m-input' ,'placeholder'=> trans('strings.backend.users.create.password.enter_your_password')]) !!}
            @if($errors->has('password'))
                <div class="form-control-feedback">{{ $errors->first('password') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
        <div class="col-lg-6 {!! $errors->has('email') ?  'has-danger' : ''!!}">
            {!! Form::label('userInputEmail', trans('strings.backend.users.create.email.email')) !!}
            {!! Form::email('email', null, ['id' => 'userInputEmail', 'class' => 'form-control m-input' , 'placeholder' => trans('strings.backend.users.create.email.enter_your_email')] ) !!}
            @if($errors->has('email'))
                <div class="form-control-feedback">{{ $errors->first('email') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
    </div>
    <div class="form-group m-form__group row">
        <div class="col-lg-6  {!! $errors->has('gender') ?  'has-danger' : ''!!} ">
            {!! Form::label('', trans('strings.backend.users.create.gender.gender')) !!}
            <div class="m-radio-inline">
                <label class="m-radio m-radio--solid">
                    {!! Form::radio('gender', 'MALE', true) !!} Male
                    <span></span>
                </label>
                <label class="m-radio m-radio--solid">
                    {!! Form::radio('gender', 'FEMALE') !!} Female
                    <span></span>
                </label>
            </div>
            @if($errors->has('gender'))
                <div class="form-control-feedback">{{ $errors->first('gender') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
        <div class="col-lg-6 {!! $errors->has('dob') ?  'has-danger' : ''!!}">
            {!! Form::label('userInputDateOfBirth', trans('strings.backend.users.create.date_of_birth.date_of_birth')) !!}
            {!! Form::text('dob', null, ['id' => 'm_datepicker_4_3', 'class'=> 'form-control m-input' , 'placeholder' => '' ]) !!}

            @if($errors->has('dob'))
                <div class="form-control-feedback">{{ $errors->first('dob') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
    </div>

    <div class="form-group m-form__group row">
        <div class="col-lg-6 {!! $errors->has('about_me') ?  'has-danger' : ''!!}">
            {!! Form::label('userInputAbout', trans('strings.backend.users.create.about')) !!}
            {!! Form::textarea('about_me', null, ['id' => 'userInputAbout', 'class'=> 'form-control m-input' , 'rows' => '3' ]) !!}
            @if($errors->has('about_me'))
                <div class="form-control-feedback">{{ $errors->first('about_me') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
        <div class="col-lg-6 {!! $errors->has('school') ?  'has-danger' : ''!!}">
            {!! Form::label('userInputSchool', trans('strings.backend.users.create.school')) !!}
            {!! Form::textarea('school', null, ['id' => 'userInputSchool', 'class'=> 'form-control m-input' , 'rows' => '3' ]) !!}
            @if($errors->has('school'))
                <div class="form-control-feedback">{{ $errors->first('school') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
    </div>
    <div class="form-group m-form__group row">
        <div class="col-lg-6 {!! $errors->has('work') ?  'has-danger' : ''!!}">
            {!! Form::label('userInputWork', trans('strings.backend.users.create.work')) !!}
            {!! Form::textarea('work', null, ['id' => 'userInputWork', 'class'=> 'form-control m-input' , 'rows' => '3' ]) !!}
            @if($errors->has('work'))
                <div class="form-control-feedback">{{ $errors->first('work') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
    </div>

    <div class="form-group m-form__group row">
        <div class="col-lg-6 {!! $errors->has('is_active') ?  'has-danger' : ''!!}">
            {!! Form::label('userInputStatus', trans('strings.backend.users.create.status')) !!}
            {!! Form::select('is_active', ['1' => 'Active', '0' => 'Not Active'] ,null, ['id' => 'userInputStatus', 'class' => 'form-control m-input']) !!}
            @if($errors->has('is_active'))
                <div class="form-control-feedback">{{ $errors->first('is_active') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
        <div class="col-lg-6 {!! $errors->has('role') ?  'has-danger' : ''!!}">
            {!! Form::label('userInputRole', trans('strings.backend.users.create.role.role')) !!}
            {!! Form::select('role', ['BASIC_USER' => 'Basic User' ,'ADMIN_USER' => 'Administrator' ] ,null, ['id' => 'userInputRole', 'class' => 'form-control m-input']) !!}
            @if($errors->has('role'))
                <div class="form-control-feedback">{{ $errors->first('role') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>

    </div>
</div>

<div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
    <div class="m-form__actions m-form__actions--solid">
        <div class="row">
            <div class="col-lg-6">
                {!! Form::submit(trans('strings.backend.users.create.save'), ['class' => 'btn btn-primary']) !!}
                <button type="reset" class="btn btn-secondary">Cancel</button>
            </div>
        </div>
    </div>
</div>