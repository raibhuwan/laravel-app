@extends('backend.index')
@section('title',  trans('strings.backend.plans.plans'))
@section('subheader')
    {{trans('strings.backend.plans.plans')}}
@endsection
@section('content')
    <div class="m-alert m-alert--icon m-alert--air m-alert--square alert alert-dismissible m--margin-bottom-30"
         role="alert">
        <div class="m-alert__icon">
            <i class="flaticon-exclamation m--font-brand"></i>
        </div>
        <div class="m-alert__text">
            {{trans('strings.backend.plans.alert_text')}}
        </div>
    </div>
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        {{ trans('strings.backend.plans.all_plans') }}
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                        <a href="{{ route('plan.create') }}"
                           class="btn btn-primary m-btn m-btn--pill m-btn--custom m-btn--icon m-btn--air">
						<span>
							<i class="la la-cart-plus"></i>
							<span>{{ trans('strings.backend.plans.add_new') }}</span>
						</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="m-portlet__body">
            <!--begin: Datatable -->
            <table class="table table-striped- table-bordered table-hover table-checkable" id="m_table_plan">
                <thead>
                <tr>
                    <th>Plan ID</th>
                    <th>Name</th>
                    <th>Plan Code</th>
                    <th>Price</th>
                    <th>Interval</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    @include('backend.plans.delete')

@endsection
@section('after-scripts')
    <script src={{asset('assets/demo/default/custom/crud/datatables/data-sources/ajax-server-side-plan.js')}}
            type="text/javascript"></script>
    <script type="text/javascript" src={{asset('/js/deletePlan.js')}}></script>
@endsection