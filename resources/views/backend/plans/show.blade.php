@extends('backend.index')
@section('title',  trans('strings.backend.plans.plans'))
@section('subheader')
    {{trans('strings.backend.plans.plans')}}
@endsection
@section('content')
    {{--<input type="hidden" value="{{ $adminPrefix }}" id="adminPrefix">--}}

    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        {{ trans('strings.backend.plans.plan') }}
                    </h3>
                </div>
            </div>

        </div>
        <div class="m-portlet__body">
            <!--begin: Datatable -->
            <table class="table table-striped- table-bordered table-hover table-checkable" id="">

                <tr>
                    <th>Plan ID</th>
                    <td>{{$data['plans']->id}}</td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td>{{$data['plans']->name}}</td>
                </tr>
                <tr>
                    <th>Plan ID</th>
                    <td>{{$data['plans']->id}}</td>
                </tr>
                <tr>
                    <th>Apple Product ID</th>
                    <td>{{$data['plans']->apple_product_id}}</td>
                </tr>
                <tr>
                    <th>Google Product ID</th>
                    <td>{{$data['plans']->google_product_id}}</td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td>{{$data['plans']->name}}</td>
                </tr>
                <tr>
                    <th>Plan Code</th>
                    <td>{{$data['plans']->plan_code}}</td>
                </tr>
                <tr>
                    <th>Interval</th>
                    <td>{{$data['plans']->interval}}</td>
                </tr>
                <tr>
                    <th>Interval Count</th>
                    <td>{{$data['plans']->interval_count}}</td>
                </tr>
                <tr>
                    <th>Sort Order</th>
                    <td>{{$data['plans']->sort_order}}</td>
                </tr>
                <tr>
                    <th>Created Date</th>
                    <td>{{$data['plans']->created_at}}</td>
                </tr>
                <tr>
                    <th>Updated Date</th>
                    <td>{{$data['plans']->updated_at}}</td>
                </tr>

            </table>
        </div>
    </div>


@endsection
