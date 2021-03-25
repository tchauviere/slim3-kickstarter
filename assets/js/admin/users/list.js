class usersList {

    constructor() {
        console.log('[usersList] Constructor');
        this.usersListDatatable = $('#users-table');
    }

    init() {
        console.log('[usersList] Init');

        this.usersListDatatable.DataTable({
            "order": [],
            "processing": false,
            "serverSide": false,
            "searching": true,
            "language": DataTableLang,
            "paging": true,
            "initComplete": function (settings, json) {

            },
        });
    }
}

$(document).ready(function(){
    console.log('[admin/users/list.js] Document ready');
    const usersListController = new usersList();
    usersListController.init();
});
