var j = jQuery.noConflict();

j(document).ready(function () {
    var modal = j('#popup-form');

    j("body").on("click", ".create_ticket", function (e) {
        modal.show();
        // Initialize CKEditor when the modal is shown
        if (!CKEDITOR.instances['descreption']) {
            CKEDITOR.replace('descreption');
        } else {
            CKEDITOR.instances['descreption'].setData('');
        }
    });

    j("body").on("click", ".close", function (e) {
        modal.hide();
        if (CKEDITOR.instances['descreption']) {
            CKEDITOR.instances['descreption'].destroy(true);
        }
    });

    // Hide the modal when clicking outside of it
    j(window).on('click', function (event) {
        if (event.target == modal[0]) {
            modal.hide();
            if (CKEDITOR.instances['descreption']) {
                CKEDITOR.instances['descreption'].destroy(true);
            }
        }
    });

});