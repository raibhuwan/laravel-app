

<div class="m-portlet__body">


    <div class="form-group m-form__group row">
        <div class="col-lg-6 {!! $errors->has('name') ?  'has-danger' : ''!!} ">
            {!! Form::label('planInputName',trans('strings.backend.plans.create.name.name')) !!}
            {!! Form::text('name',null,['id' => 'userInputName', 'class' => 'form-control m-input' , 'placeholder' => trans("strings.backend.plans.create.name.enter_name") ]) !!}
            @if($errors->has('name'))
            <div class="form-control-feedback">{{ $errors->first('name') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>

    </div>
    <div class="form-group m-form__group row">
        <div class="col-lg-6 {!! $errors->has('google_product_id') ?  'has-danger' : ''!!} ">
            {!! Form::label('planInputGoogleProductId',trans('strings.backend.plans.create.google_product_id.google_product_id')) !!}
            {!! Form::text('google_product_id',null,['id'=>'planInputGoogleProductId', 'class' => 'form-control m-input' , 'placeholder' => trans("strings.backend.plans.create.google_product_id.enter_google_product_id") ]) !!}
            @if($errors->has('google_product_id'))
                <div class="form-control-feedback">{{ $errors->first('google_product_id') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
        <div class="col-lg-6 {!! $errors->has('apple_product_id') ?  'has-danger' : ''!!}">
            {!! Form::label('planInputAppleProductId',trans('strings.backend.plans.create.apple_product_id.apple_product_id')) !!}
            {!! Form::text('apple_product_id',null,['id'=>'planInputAppleProductId', 'class' => 'form-control m-input' , 'placeholder' => trans("strings.backend.plans.create.apple_product_id.enter_apple_product_id") ]) !!}
            @if($errors->has('apple_product_id'))
                <div class="form-control-feedback">{{ $errors->first('apple_product_id') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
    </div>
    <div class="form-group m-form__group row">
        <div class="col-lg-6 {!! $errors->has('plan_code') ?  'has-danger' : ''!!}">
            {!! Form::label('planInputPlanCode',trans('strings.backend.plans.create.plan_code.plan_code')) !!}
            {!! Form::text('plan_code',null,['id' => 'planInputPlanCode', 'class' => 'form-control m-input' , 'placeholder' => trans("strings.backend.plans.create.plan_code.enter_plan_code") ]) !!}
            @if($errors->has('plan_code'))
                <div class="form-control-feedback">{{ $errors->first('plan_code') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
        <div class="col-lg-6 {!! $errors->has('price') ?  'has-danger' : ''!!} ">
            {!! Form::label('planInputPrice',trans('strings.backend.plans.create.price.price')) !!}
            {!! Form::text('price',null,['id' => 'planInputPrice', 'class' => 'form-control m-input' , 'placeholder' => trans("strings.backend.plans.create.price.enter_price")]) !!}
            @if($errors->has('price'))
                <div class="form-control-feedback">{{ $errors->first('price') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
    </div>
    <div class="form-group m-form__group row">
        <div class="col-lg-6 {!! $errors->has('interval') ?  'has-danger' : ''!!} ">
            {!! Form::label('planInputInterval', trans('strings.backend.plans.create.interval.interval')) !!}
            {!! Form::select('interval',$data['select'] ,null, ['id' => 'planInputInterval', 'class' => 'form-control m-input']) !!}
            @if($errors->has('interval'))
                <div class="form-control-feedback">{{ $errors->first('interval') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
        <div class="col-lg-6 {!! $errors->has('interval_count') ?  'has-danger' : ''!!}">
            {!! Form::label('planInputIntervalCount',trans('strings.backend.plans.create.interval_count.interval_count')) !!}
            {!! Form::text('interval_count',null,['id' => 'planInputIntervalCount', 'class' => 'form-control m-input' , 'placeholder' => "Enter Interval Count" ]) !!}
            @if($errors->has('interval_count'))
                <div class="form-control-feedback">{{ $errors->first('interval_count') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
    </div>

    <div class="form-group m-form__group row">
        <div class="col-lg-6 {!! $errors->has('description') ?  'has-danger' : ''!!}">
            {!! Form::label('planInputDescription',trans('strings.backend.plans.create.description')) !!}
            {!! Form::textarea('description',null,['id' => 'planInputDescription', 'class' => 'form-control m-input'  ]) !!}
            @if($errors->has('description'))
            <div class="form-control-feedback">{{ $errors->first('description') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
    </div>
</div>
<div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
    <div class="m-form__actions m-form__actions--solid">
        <div class="row">
            <div class="col-lg-6">
                {!! Form::submit(trans('strings.backend.plans.create.save'), ['class' => 'btn btn-primary']) !!}
                <a href="{{route('plan.index')}}" class="btn btn-secondary">Cancel</a>
            </div>

        </div>
    </div>
</div>

