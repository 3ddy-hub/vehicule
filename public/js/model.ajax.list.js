$(document).ready(function () {
    $("#model-list").DataTable({
        "language": {
            url: datatable_lannguage
        },
        "aaSorting": [],
        "bProcessing": true,
        "bFilter": true,
        "bServerSide": true,
        "lengthMenu": [[10, 50, 100, 500, 1000, 5000, 10000], [10, 50, 100, 500, '1 000', '5 000', '10 000']],
        "iDisplayLength": 10,
        "responsive": true,
        "ajax": {
            url: url_model_ajax_list,
            data: function (data) {
                if (data.order[0])
                    data.order_by = data.columns[data.order[0].column].name + ' ' + data.order[0].dir;
            }
        },
        "columnDefs": [
            {
                name: "modele",
                targets: 0
            },
            {
                name: "marque",
                targets: 1
            },
            {
                name: "puissance",
                targets: 2
            },
            {
                name: "carburant",
                targets: 3
            },
            {
                name: "model_id",
                orderable: false,
                targets: 4,
                render: function (data) {
                    href_edit = edit_path.replace('0', data);
                    href_delete = delete_path.replace('0', data);

                    let actions = '<td>' +
                        ' <a href="' + href_edit + '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i>' + update_text + '</a>' +
                        ' <a href="' + href_delete + '" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> ' + delete_text + '</a></div></td>';
                    return actions;
                }
            }
        ]
    });
});