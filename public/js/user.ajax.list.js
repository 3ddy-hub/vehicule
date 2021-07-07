$(document).ready(function () {
    $("#user-list").DataTable({
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
            url: url_user_ajax_list,
            data: function (data) {
                if (data.order[0])
                    data.order_by = data.columns[data.order[0].column].name + ' ' + data.order[0].dir;
            }
        },
        "columnDefs": [
            {
                name: "profil",
                targets: 0
            },
            {
                name: "username",
                targets: 1
            },
            {
                name: "email",
                targets: 2
            },
            {
                name: "role",
                targets: 3,
                render: function (data) {
                    return "<span>" + user_text + "</span>"
                }
            },
            {
                name: "date_create",
                targets: 4
            },
            {
                name: "user_id",
                orderable: false,
                targets: 5,
                render: function (data) {
                    href_edit = edit_path.replace('0', data);
                    href_view = view_path.replace('0', data);
                    href_delete = delete_path.replace('0', data);

                    let actions = '<td><div class="btn-group">' +
                        ' <a href="' + href_view + '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> ' + view_text + '</a>' +
                        ' <button type="button" class="btn btn-info btn-flat dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">' +
                        ' <span class="sr-only">Toggle Dropdown</span></button>' +
                        ' <div class="dropdown-menu" role="menu" style="">' +
                        ' <a href="' + href_edit + '" class="btn btn-sm dropdown-item" href="#"><i class="fa fa-edit"></i>' + update_text + '</a>' +
                        ' <a href="' + href_delete + '" onclick="confirm(confirm_delete_text)" class="btn btn-sm dropdown-item" href="#"><i class="fa fa-trash"></i> ' + delete_text + '</a></div></div></td>';
                    return actions;
                }
            }
        ]
    });
})