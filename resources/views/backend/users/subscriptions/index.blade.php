@extends('backend.users.layout')
@section('title', trans('strings.backend.plan_subscription.subscriptions'))
@section('subheader')
    {{ trans('strings.backend.plan_subscription.subscriptions') }}
@endsection
@section('users.layout')
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        {{ trans('strings.backend.plan_subscription.subscriptions') }}
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                        <a href="{{ route('user.subscription.create',$userDetails->user_detail_id) }}"
                           class="btn btn-primary m-btn m-btn--pill m-btn--custom m-btn--icon m-btn--air">
						<span>
							<i class="la la-cart-plus"></i>
							<span>{{ trans('strings.backend.plan_subscription.add_new') }}</span>
						</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="m-portlet__body">
            <!--begin: Datatable -->
            <table class="table table-striped- table-bordered table-hover table-checkable" id="m_table_subscription">
                <thead>
                <tr>
                    <th>Subscription ID</th>
                    <th>Plan ID</th>
                    <th>Name</th>
                    <th>Starts At</th>
                    <th>Ends At</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    @include('backend.users.subscriptions.delete')
@endsection

@section('after-scripts')
    <script src={{asset('assets/demo/default/custom/crud/datatables/data-sources/ajax-server-side-subscription.js')}}
            type="text/javascript"></script>
    <script type="text/javascript" src={{asset('/js/deleteSubscription.js')}}></script>
@endsection
