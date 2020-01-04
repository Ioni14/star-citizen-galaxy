import '../css/main.scss';
import '@coreui/coreui';
import '@fortawesome/fontawesome-free';
import 'datatables.net-bs4';
import 'select2';
import $ from "jquery";

function addNewRowCollection($collection, $rowsContainer, initRow = null)
{
    const collectionPrototype = $collection.data('prototype');
    const collectionIndex = $collection.data('index');

    const $newRowForm = $(collectionPrototype.replace(/__name__/g, collectionIndex));
    $collection.data('index', collectionIndex + 1);

    if (initRow !== null) {
        initRow($newRowForm);
    }

    $rowsContainer.append($newRowForm);
}

$(document).ready(function () {
    $('#js-ships-datatable').DataTable({
        paging: false,
        columnDefs: [
            {
                targets: 'nosort',
                orderable: false
            },
        ],
        order: [[1, "asc"]]
    });

    const select2Options = {
        theme: 'bootstrap',
    };
    $('.js-select2').select2(select2Options);

    $('.custom-file-input').on('change', function () {
        $(this).next('.custom-file-label').html($(this)[0].files[0].name);
    });

    $('#js-holded-ships-collection').on('click', '.js-holded-ships-add-row', (ev) => {
        addNewRowCollection($('#js-holded-ships-collection'), $('#js-holded-ships-rows'), ($newRow) => {
            $newRow.find('.js-select2').select2(select2Options);
            $newRow.on('click', '.js-holded-ships-delete-row', (ev) => {
                $(ev.currentTarget).closest('.row').remove();
            });
        });
    });
    $('#js-holded-ships-collection').on('click', '.js-holded-ships-delete-row', (ev) => {
        $(ev.currentTarget).closest('.row').remove();
    });
});
