var j = jQuery.noConflict();

j(document).ready(function () {
    var originalValues = {};
    var editUrl = null;
    var itemId = null;
    var formKey = null;
    var reasons = null;
    var isEditing = false;
    var isMassActionEnabled = false;

    j("body").on("click", ".edit-row", function (e) {
        e.preventDefault();

        if (isEditing) {
            return;
        }

        var editButton = j(this);
        editUrl = editButton.data('url');
        itemId = editButton.data('repricer-id');
        formKey = editButton.data('form-key');
        reasons = editButton.data('reason');

        // console.log(reasons);

        var className = "editable-" + itemId;
        var selector = "td." + className;

        var editableCells = j(selector);
        // console.log(Array.isArray(editableCells));
        editableCells.each(function () {
            var field = j(this).data("field");
            originalValues[field] = j(this).text().trim();
            if (field === "reason" && originalValues[field] !== "no match") {
                var select = '<select class="edit-input">';
                for (var key in reasons) {
                    if (reasons.hasOwnProperty(key)) {
                        select += '<option value="' + key + '"' + (originalValues[field] === reasons[key] ? ' selected' : '') + '>' + reasons[key] + '</option>';
                    }
                }
                select += '</select>';
                j(this).html(select);
            } else if (field !== "reason") {
                j(this).html('<input type="text" class="edit-input" value="' + originalValues[field] + '">');
            }
        });


        editButton.hide();

        var saveButton = j('<a style="padding-right:5px" href="#" class="save-button">Save</a>');
        var cancelButton = j('<a href="#" class="cancel-button">Cancel</a>');
        var buttonContainer = j('<div>').addClass('button-container').append(saveButton, cancelButton);

        var cell = editButton.closest('td');
        cell.empty().append(buttonContainer);

        editableCells.first().find('.edit-input').focus();
        isEditing = true;
    });

    j("body").on("click", ".save-button", function (e) {
        e.preventDefault();
        var saveButton = j(this);

        var className = "editable-" + itemId;
        var selector = "td." + className;

        var editableCells = j(selector);
        var editedData = {};

        editableCells.each(function () {
            var field = j(this).data('field');
            var editInput = j(this).find('.edit-input')
            if (editInput.length > 0) {
                var value = editInput.val().trim();
                editedData[field] = value;
                if (field === "reason") {
                    console.log(123);
                    j(this).text(reasons[value]);
                } else {
                    console.log(456);
                    j(this).text(value);
                }
            }
            if (field == 'reason' && editInput.length == 0) {
                editedData['reason'] = 1;
            }
        });

        j.ajax({
            url: editUrl,
            type: 'POST',
            // dataType: 'json', 
            data: {
                form_key: formKey,
                itemId: itemId,
                editedData: editedData
            },
            success: function (response) {
                console.log('Data saved successfully:', response);
            },
            error: function (xhr, status, error) {
                console.log(status);
                console.error('Error saving data:', error);
            }
        });

        editableCells.removeAttr('contenteditable');

        var cell = saveButton.closest('td');
        reasons = JSON.stringify(reasons);
        cell.empty().html("<a href='#' class='edit-row' data-url='" + editUrl + "' data-repricer-id='" + itemId + "' data-reason='" + reasons + "'>Edit</a>");
        isEditing = false;
    });

    j("body").on("click", ".cancel-button", function (e) {
        e.preventDefault();
        var cancelButton = j(this);

        var className = "editable-" + itemId;
        var selector = "td." + className;

        var editableCells = j(selector);

        editableCells.each(function () {
            var field = j(this).data('field');
            j(this).text(originalValues[field]);
        });

        // editableCells.removeAttr('contenteditable');
        var cell = cancelButton.closest('td');
        reasons = JSON.stringify(reasons);
        cell.empty().html("<a href='#' class='edit-row' data-url='" + editUrl + "' data-repricer-id='" + itemId + "' data-reason='" + reasons + "'>Edit</a>");
        isEditing = false;

    });
    // j(".headmassaction").addClass("massaction-header-class");
    j("[name='massaction']").hide(); 
    j(".massaction-checkbox").hide(); 
    j(".massaction").hide(); 
    j("body").on("click", ".enable_mass_update", function (e) {
        e.preventDefault();
        
        var button = j(this);
        isMassActionEnabled = !isMassActionEnabled;

        
        // Change the button label based on the state
        if (isMassActionEnabled) {
            j("[name='massaction']").show();
            j(".massaction-checkbox").show();
            j(".massaction").show();
            button.text('Disable Mass Action');
        } else {
            j("[name='massaction']").hide();
            j(".massaction-checkbox").hide();
            j(".massaction").hide();
            button.text('Enable Mass Action');
        }

    });

});
// var isMassActionEnabled = false;

// function toggleMassAction() {
//     var button = j('enable_mass_update'); // The "Enable Mass Update" button
//     var checkboxes = j('.mass_select'); // Select all checkboxes in the grid

//     isMassActionEnabled = !isMassActionEnabled; // Toggle the state
//     console.log(button);
//     checkboxes.each(function (checkbox) {
//         console.log(123);
//         checkbox.hide(); // Show or hide the checkboxes based on the state
//     });

//     // Change the button label based on the state
//     if (isMassActionEnabled) {
//         button.innerHTML = 'Disable Mass Update';
//     } else {
//         button.innerHTML = 'Enable Mass Update';
//     }
// }