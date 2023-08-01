<div class="m-portlet__body">
    <div class="form-group m-form__group row ">
        <div class="col-lg-6 {!! $errors->has('name') ?  'has-danger' : ''!!}">
            {!! Form::label('subscriptionInputPlanId',trans('strings.backend.plan_subscription.edit.plan_id.plan_id')) !!}
            {!! Form::select('plan_id',$data['plan'],null,['id'=>'subscriptionInputPlanId', 'class' => 'form-control m-input' ]) !!}
            @if($errors->has('plan_id'))
                <div class="form-control-feedback">{{ $errors->first('plan_id') }}</div>
            @endif
            <span class="m-form__help"></span>
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
</div>