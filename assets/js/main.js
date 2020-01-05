import '../css/main.scss';
import '@coreui/coreui';
import '@fortawesome/fontawesome-free';
import 'datatables.net-bs4';
import 'select2';
import $ from "jquery";

$(document).ready(function () {
    const select2Options = {
        theme: 'bootstrap',
    };
    $('.js-select2').select2(select2Options);

    $('.custom-file-input').on('change', function () {
        $(this).next('.custom-file-label').html($(this)[0].files[0].name);
    });
});
