@extends('backend.index')
@section('title', 'Users')
@section('subheader')
    {{ trans('menus.backend.aside-left.users.reported_users') }}
@endsection
@section('content')

    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        {{ trans('strings.backend.users.reported_users') }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">
            <!--begin: Datatable -->
            <table class="table table-striped- table-bordered table-hover table-checkable" id="m_table_reported_user">
                <thead>
                <tr>
                    <th>Report ID</th>
                    <th>Reported by</th>
                    <th>Reported to</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@section('after-scripts')
    <script src="{{ asset('assets/demo/default/custom/crud/datatables/data-sources/ajax-server-side-reported-user.js') }}"
            type="text/javascript"></script>
@endsection
