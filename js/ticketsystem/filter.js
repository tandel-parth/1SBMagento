var j = jQuery.noConflict();
j(document).ready(function () {
    var modal = j('.modal-filter');

    j("body").on("click", ".enable_filters", function (e) {
        modal.show();
    });

    j("body").on("click", ".close", function (e) {
        modal.hide();
    });

    // Hide the modal when clicking outside of it
    j(window).on('click', function (event) {
        if (event.target == modal[0]) {
            modal.hide();
        }
    });
});

function applyFilters(filterId, url) {
    // AJAX request to apply filters
    new Ajax.Request(url, {
        method: 'post',
        dataType: "json",
        parameters: { filter_id: filterId },
        onSuccess: function (response) {
            console.log(response.responseText);
            var gridContainer = j('.table-container');
            gridContainer.html(response.responseText);
        },
        onFailure: function () {
            alert('Failed to apply filters.');
        }
    });
}
