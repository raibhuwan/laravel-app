<div class="m-portlet__body">
    <div class="form-group m-form__group row">
        <div class="col-lg-6 {!! $errors->has('plan_id') ?  'has-danger' : ''!!} ">
            {!! Form::label('subscriptionInputPlanId',trans('strings.backend.plan_subscription.edit.plan_id.plan_id')) !!}
            {!! Form::select('plan_id',$data['plan'],null,['id'=>'subscriptionInputPlanId', 'class' => 'form-control m-input' ]) !!}
            @if($errors->has('plan_id'))
                <div class="form-control-feedback">{{ $errors->first('plan_id') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
    </div>
    <div class="form-group m-form__group row">
        <div class="col-lg-6 {!! $errors->has('starts_at') ?  'has-danger' : ''!!}">
            {!! Form::label('subscriptionStartsAt',trans('strings.backend.plan_subscription.edit.starts_at.starts_at')) !!}
            {!! Form::text('starts_at',null,[ 'class' => 'form-control m-input datetimepicker' ]) !!}
            @if($errors->has('starts_at'))
                <div class="form-control-feedback">{{ $errors->first('starts_at') }}</div>
            @endif
            <span class="m-form__help"></span>
        </div>
        <div class="col-lg-6 {!! $errors->has('ends_at') ?  'has-danger' : ''!!}">
            {!! Form::label('subscriptionEndsAt',trans('strings.backend.plan_subscription.edit.ends_at.ends_at')) !!}
            {!! Form::text('ends_at',null,['class' => 'form-control m-input datetimepicker' ]) !!}
            @if($errors->has('ends_at'))
                <div class="form-control-feedback">{{ $errors->first('ends_at') }}</div>
            @endif
        </div>
    </div>

</div>
<div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
    <div class="m-form__actions m-form__actions--solid">
        <div class="row">
            <div class="col-lg-6">
                {!! Form::submit(trans('strings.backend.plan_subscription.edit.save'), ['class' => 'btn btn-primary']) !!}
                <a href="{{route('user.index')}}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </div>
</div>

@section('after-scripts')
    <script type="text/javascript" src="<?php echo e(asset('js/datetimepicker.js')); ?>"></script>
@endsection
