@extends('backend.users.layout')
@section('title', trans('strings.backend.plan_subscription.subscriptions'))
@section('subheader')
    {{ trans('strings.backend.plan_subscription.subscriptions') }}
@endsection
@section('users.layout')
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__body">
            <!--begin: Datatable -->
            <table class="table table-striped- table-bordered table-hover table-checkable" id="">
                <tr>
                    <th>Subscription ID</th>
                    <td>{{$data['subscription']->id}}</td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td>{{$data['subscription']->name}}</td>
                </tr>
                <tr>
                    <th>Subscribable ID</th>
                    <td>{{$data['subscription']->subscribable_id}}</td>
                </tr>
                <tr>
                    <th>Subscribable Type</th>
                    <td>{{$data['subscription']->subscribable_type}}</td>
                </tr>
                <tr>
                    <th>Plan ID</th>
                    <td>{{$data['subscription']->plan_id}}</td>
                </tr>
                <tr>
                    <th>Trials End At</th>
                    <td>{{$data['subscription']->trial_ends_at}}</td>
                </tr>
                <tr>
                    <th>Starts At</th>
                    <td>{{$data['subscription']->starts_at}}</td>
                </tr>
                <tr>
                    <th>Ends At</th>
                    <td>{{$data['subscription']->ends_at}}</td>
                </tr>
                <tr>
                    <th>Canceled At</th>
                    <td>{{$data['subscription']->canceled_at}}</td>
                </tr>
                <tr>
                    <th>Created Date</th>
                    <td>{{$data['subscription']->created_at}}</td>
                </tr>
                <tr>
                    <th>Updated Date</th>
                    <td>{{$data['subscription']->updated_at}}</td>
                </tr>
            </table>
        </div>
    </div>
@endsection
