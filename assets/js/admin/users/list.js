class usersList {

    constructor() {
        console.log('[usersList] Constructor');
        this.usersListDatatable = $('#users-table');
    }

    init() {
        console.log('[usersList] Init');

        $('#users-table').DataTable({
            "initComplete": function( settings, json) {

            },
            "processing": true,
            "paging": true,
            "ordering": false,
            "serverSide": true,
            "language": DataTableLang,
            "ajax": {
                "url": appUrls.getUsersPaging,
                "type": "POST",
                "data": function ( d ) {
                   /* d.tableID = tabPaneIDToLoad;
                    d.maxTotal = parseInt($(tabPaneIDToLoad+'-tab > span').text());*/
                   console.log('data recevied', d);
                }
            },
            "columnDefs": this.getColumnDef(),
            "columns": this.getColumns(),
        });

    }

    getColumnDef() {
        return [
            {"className": "dt-center", "targets": "_all"},
            {
                "targets": 1, // ID
                "data": null,
                "render": function ( data, type, row, meta ) {
                    return data.id;
                }
            },
            {
                "targets": 2, // Firstname
                "data": null,
                "render": function ( data, type, row, meta ) {
                    return data.firstname;
                }
            },
            {
                "targets": 3, // Lastname
                "data": null,
                "render": function ( data, type, row, meta ) {
                    return data.firstname;
                }
            },
            {
                "targets": 4, // Email
                "data": null,
                "render": function ( data, type, row, meta ) {
                    return data.email;
                }
            },
            {
                "targets": 5, // Type
                "data": null,
                "render": function ( data, type, row, meta ) {
                    if (data.role.name == 'superadmin') {
                        return '<span class="badge badge-pill badge-primary"><i class="fa fa-user-shield"></i>&nbsp;' + data.role.name.toUpperCase() + '</span>';
                    } else if (data.role.name == 'admin') {
                        return '<span class="badge badge-pill badge-secondary"><i class="fa fa-user-tie"></i>&nbsp;' + data.role.name.toUpperCase() + '</span>';
                    } else if (data.role.name == 'user') {
                        return '<span class="badge badge-pill badge-info"><i class="fa fa-user"></i>&nbsp;' + data.role.name.toUpperCase() + '</span>';
                    } else {
                        return '<span class="badge badge-pill badge-danger"><i class="fa fa-exclamation-triangle"></i>&nbsp;' + data.role.name.toUpperCase() + '</span>';
                    }
                }
            },
            {
                "targets": 6, // Is Activated ?
                "data": null,
                "render": function ( data, type, row, meta ) {
                    if (data.is_activated) {
                        return '<span class="badge badge-pill badge-success p-2"><i class="fa fa-check"></i></span>';
                    } else {
                        return '<span class="badge badge-pill badge-danger p-2"><i class="fa fa-times"></i></span>';
                    }
                }
            },
            {
                "targets": 7, // Created At ?
                "data": null,
                "render": function ( data, type, row, meta ) {
                    return moment(data.created_at).format('DD/MM/YYYY HH:mm:ss');
                }
            },
            {
                "targets": 8, // Activated At ?
                "data": null,
                "render": function ( data, type, row, meta ) {
                    if (data.activated_at) {
                        return moment(data.activated_at).format('DD/MM/YYYY HH:mm:ss');
                    } else {
                        return '<span class="badge badge-pill badge-danger p-2"><i class="fa fa-times"></i></span>';
                    }
                }
            },
            {
                "targets": 9, // Updated At ?
                "data": null,
                "render": function ( data, type, row, meta ) {
                    return moment(data.updated_at).format('DD/MM/YYYY HH:mm:ss');
                }
            },
            {
                "targets": 10, // Buttons ?
                "data": null,
                "render": function ( data, type, row, meta ) {
                   return '';
                }
            }
        ];
    }

    getColumns() {
        return [
            { "data": "id" },
            { "data": "firstname"},
            { "data": "lastname"},
            { "data": "email" },
            { "data": "type" },
            { "data": "is_activated" },
            { "data": "created_at" },
            { "data": "activated_at" },
            { "data": "updated_at" },
            { "data": null},
        ];
    }
}

$(document).ready(function(){
    console.log('[admin/users/list.js] Document ready');
    const usersListController = new usersList();
    usersListController.init();
});
