var DatatablesDataSourceAjaxClient = {
    init: function () {
        $("#m_table_user").DataTable({
            responsive: !0,
            ajax: {
                url: "/readUser",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                data: {pagination: {perpage: 50}}
            },
            columns: [{data: "id"}, {data: "name"}, {data: "country_code"}, {data: "phone"}, {data: "email"}, {data: "gender"}, {data: "dob"}, {data: "role"}, {data: "is_active"}, {data: "Actions"}],
            columnDefs: [{
                targets: -1, title: "Actions", orderable: !1, render: function (a, t, e, n) {
                    return '\n                        <span class="dropdown">\n                            <a href="#" class="btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown" aria-expanded="true">\n                              <i class="la la-ellipsis-h"></i>\n                            </a>\n                            <div class="dropdown-menu dropdown-menu-right">\n                                <a class="dropdown-item" href="#"><i class="la la-edit"></i> Edit Details</a>\n                                <a class="dropdown-item" href="#"><i class="la la-leaf"></i> Update Status</a>\n                                <a class="dropdown-item" href="#"><i class="la la-print"></i> Generate Report</a>\n                            </div>\n                        </span>\n                        <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" title="View">\n                          <i class="la la-edit"></i>\n                        </a>'
                }
            }, {
                targets: 9, render: function (a, t, e, n) {
                    var s = {
                        1: {title: "Active", class: "m-badge--success"},
                        0: {title: "Not Active", class: "m-badge--warning"},
                    };
                    return void 0 === s[a] ? a : '<span class="m-badge ' + s[a].class + ' m-badge--wide">' + s[a].title + "</span>"
                }
            }, {
                targets: 8, render: function (a, t, e, n) {
                    var s = {
                        "BASIC_USER": {title: "Basic User", state: "primary"},
                        "ADMIN_USER": {title: "Admin User", state: "accent"},
                    };
                    return void 0 === s[a] ? a : '<span class="m-badge m-badge--' + s[a].state + ' m-badge--dot"></span>&nbsp;<span class="m--font-bold m--font-' + s[a].state + '">' + s[a].title + "</span>"
                }
            }]
        })
    }
};
jQuery(document).ready(function () {
    DatatablesDataSourceAjaxClient.init()
});