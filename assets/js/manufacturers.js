import '@coreui/coreui';
import '@fortawesome/fontawesome-free';
import 'datatables.net-bs4';
import $ from "jquery";

$(document).ready(function () {
    $('#js-manufacturers-datatable').DataTable({
        paging: false,
        columnDefs: [
            {
                targets: 'nosort',
                orderable: false
            },
        ],
    });
});
