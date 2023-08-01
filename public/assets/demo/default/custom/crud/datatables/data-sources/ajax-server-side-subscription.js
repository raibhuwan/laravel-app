function setId(id) {
    jQuery('#hidden_subscription_id').val(id);
}

var reloadSubscription = false;

var DatatablesDataSourceSubscriptionAjaxServer = function () {
    var initTable1 = function () {
        var table = $('#m_table_subscription');
        var user_id = jQuery('input[name=user_id]').val();
        var ajax_url = jQuery('meta[name=site_url ]').attr("content");
        var url = ajax_url + "/readsubscription";
        return table.DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
                data: {
                    user_id: user_id,
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'plan_id', name: 'plan_id'},
                {data: 'name', name: 'name'},
                {data: 'starts_at', name: 'starts_at'},
                {data: 'ends_at', name: 'ends_at'},
                {data: "actions", searchable: false}
            ],
            columnDefs: [
                {
                    targets: -1,
                    title: "Actions",
                    orderable: false,
                    render: function (data, type, full, meta) {
                        return `
                                <span class="dropdown">
                                   <a href="#" class="btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown" aria-expanded="true">
                                        <i class="la la-ellipsis-h"></i>
                                   </a>
                                   <div class="dropdown-menu dropdown-menu-right">
                                        <!--<a class="dropdown-item" href="user/` + full.id + `/edit"><i class="la la-edit"></i> Edit Details</a>-->
                                        <a class="dropdown-item" href=""  data-toggle="modal" data-target="#modal1"  onclick="setId(` + full.id + `)" title="Delete" >
                                            <i class="la la-trash"></i>Delete
                                        </a>
                                        <a class="dropdown-item" href="` + ajax_url + `/user/` + user_id + `/edit/` + full.id + `/subscription" class="m-portlet__nav-link btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" title="Edit">
                                            <i class="la la-edit"></i>Edit
                                        </a>
                                        <a class="dropdown-item" href="` + ajax_url + `/user/` + user_id + `/subscription/` + full.id + `" class="m-portlet__nav-link btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" title="View">
                                            <i class="fa fa-eye"></i>View
                                        </a>  
                                   </div>
                                </span>
                                `;
                    }
                }
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
    reloadSubscription = DatatablesDataSourceSubscriptionAjaxServer.init();
});
