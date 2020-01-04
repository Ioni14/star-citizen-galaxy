import '../css/main.scss';
import '@coreui/coreui';
import '@fortawesome/fontawesome-free';
// import 'datatables.net';
import 'datatables.net-bs4';

$(document).ready(function() {
    $('#js-ships-datatable').DataTable({
        "paging": false,
        "columnDefs": [
            {
                "targets": 'nosort',
                "orderable": false
            },
        ],
        "order": [[ 1, "asc" ]]
    });
});
