function setId(id) {
    jQuery('#hidden_plan_id').val(id);
}

var reloadPlan = false;

var DatatablesDataSourcePlanAjaxServer = function () {
    var initTable1 = function () {
        var table = $('#m_table_plan');

        return table.DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: "readplan",
            columns: [
                {data: 'id'},
                {data: 'name'},
                {data: 'plan_code'},
                {data: 'price'},
                {data: 'interval'},
                {data: 'description'},
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
                                        <!--<a class="dropdown-item" href="plan/` + full.id + `/edit"><i class="la la-edit"></i> Edit Details</a>-->
                                        <a class="dropdown-item" href=""  data-toggle="modal" data-target="#modal1" onclick="setId(` + full.id + `)">
                                        <i class="la la-trash"></i> Delete</a>
                                   </div>
                                </span>
                                <a href="plan/` + full.id + `/edit" class="m-portlet__nav-link btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" title="Edit">
                                    <i class="la la-edit"></i>
                                </a>
                                <a href="plan/` + full.id + `" class="m-portlet__nav-link btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" title="View">
                                    <i class="fa fa-eye"></i>
                                </a>  
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
    reloadPlan = DatatablesDataSourcePlanAjaxServer.init();
});
