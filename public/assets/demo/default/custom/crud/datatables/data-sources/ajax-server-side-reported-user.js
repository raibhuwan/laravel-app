var DatatablesDataSourceReportedUserAjaxServer = {

    init: function () {
        $("#m_table_reported_user").DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: "user/read",
            columns: [
                {data: "id"},
                {data: "reported_by"},
                {data: "reported_to"},
                {data: "reason"},
                {data: "actions", searchable: false}
            ],
            columnDefs: [{
                targets: -1,
                title: 'Actions',
                orderable: false,
                render: function (data, type, full, meta) {
                    return `
                        <span class="dropdown">
                            <a href="#" class="btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown" aria-expanded="true">
                              <i class="la la-ellipsis-h"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="/admin/report/user/` + full.id + `"><i class="la la-eye"></i> View</a>
                                <a class="dropdown-item" href="#"><i class="la la-trash"></i> Delete</a>
                            </div>
                        </span>
                        `;
                }
            }
            ]
        })
    }
};
jQuery(document).ready(function () {
    DatatablesDataSourceReportedUserAjaxServer.init()
});
