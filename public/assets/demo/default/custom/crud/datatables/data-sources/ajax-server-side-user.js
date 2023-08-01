function setId(id) {
    jQuery('#hidden_user_id').val(id);
}

var reloadUser = false;

var DatatablesDataSourceUserAjaxServer = function () {
    var initTable1 = function () {
        var table = $('#m_table_user');
        var ajax_url = $('meta[name=site_url ]').attr("content");

        return table.DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: `${ajax_url}/user/read`,
            columns: [
                {data: "id"},
                {data: "name"},
                {data: "country_code"},
                {data: "phone"},
                {data: "email"},
                {data: "gender"},
                {data: "dob"},
                {data: "role"},
                {data: "is_active"},
                {data: "actions", searchable: false}
            ],
            columnDefs: [
                {
                    targets: -1,
                    title: "Actions",
                    orderable: false,
                    render: function (data, type, full, meta) {
                        return `<span class="dropdown">
                                   <a href="#" class="btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown" aria-expanded="true">
                                        <i class="la la-ellipsis-h"></i>
                                   </a>
                                   <div class="dropdown-menu dropdown-menu-right">
                                        <!--<a class="dropdown-item" href="user/` + full.id + `/edit"><i class="la la-edit"></i> Edit Details</a>-->
                                        <a class="dropdown-item" href=""  data-toggle="modal" data-target="#modal1" onclick="setId(` + full.id + `)">
                                        <i class="la la-trash"></i> Delete</a>
                                   </div>
                                </span>
                                <a href="${ajax_url}/user/${full.id}/edit" class="m-portlet__nav-link btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" title="Edit">
                                    <i class="la la-edit"></i>                        
                                </a>`;
                    }
                },
                {
                    targets: 8,
                    render: function (data, type, full, meta) {
                        var status = {
                            1: {title: "Active", class: "m-badge--success"},
                            0: {title: "Not-Active", class: "m-badge--warning"},
                        };
                        if (typeof status[data] === 'undefined') {
                            return data;
                        }
                        return '<span class="m-badge ' + status[data].class + ' m-badge--wide">' + status[data].title + '</span>';
                    },
                },
                {
                    targets: 7,
                    render: function (data, type, full, meta) {
                        var status = {
                            "BASIC_USER": {title: "Basic User", state: "primary"},
                            "ADMIN_USER": {title: "Admin User", state: "accent"},
                        };
                        if (typeof status[data] === 'undefined') {
                            return data;
                        }
                        return '<span class="m-badge m-badge--' + status[data].state + ' m-badge--dot"></span>&nbsp;' +
                            '<span class="m--font-bold m--font-' + status[data].state + '">' + status[data].title + '</span>';
                    },
                },
            ]
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            return initTable1();
        },
    };
}();

jQuery(document).ready(function () {
    reloadUser = DatatablesDataSourceUserAjaxServer.init();
});