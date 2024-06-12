var j = jQuery.noConflict();

j(document).ready(function () {
    j("body").on("click", "#delete_file", function (e) {
        e.preventDefault(); // Change 'event' to 'e'
        var deleteButton = j(this);
        var deletedFile = deleteButton.data("file-path");
        var deletedUrl = deleteButton.data("url");
        console.log(deletedUrl);
        j.ajax({
            url: deletedUrl,
            type: "POST",
            dataType: "json",
            data: {
                form_key: FORM_KEY,
                deletedFile: deletedFile,
            },
            success: function (response) {
                console.log("File Deleted successfully:", response);
                if (response) {
                    deleteButton.closest('tr').remove();
                    alert('File Delete successfully');
                }
            },
            error: function (xhr, status, error) {
                console.log(status);
                console.error("Error Deleted data:", error);
            },
        });
    });

    j('body').on('click', '.editable', function (e) {
        e.preventDefault();
        var editButton = j(this);

        // Check if the input field and buttons already exist
        if (!editButton.hasClass('editing')) {
            let originalValue = editButton.text().trim();
            editButton.html(
                '<input type="text" class="edit-input" value="' + originalValue + '" data-original="' + originalValue + '">'
            );

            // Create save and cancel buttons using jQuery
            var saveButton = j('<a>', {
                href: '#',
                'class': 'save-button'
            }).text('Save');

            var cancelButton = j('<a>', {
                href: '#',
                'class': 'cancel-button',
                'style': 'padding-left:5px'
            }).text('Cancel');

            // Append buttons after the editable element
            editButton.after(cancelButton);
            editButton.after(saveButton);

            // Add a class to mark that editing is in progress
            editButton.addClass('editing');
        }
    });

    // Handle cancel button click
    j('body').on('click', '.cancel-button', function (e) {
        e.preventDefault();
        var cancelButton = j(this);
        var editButton = cancelButton.prev('.save-button').prev('.editable');
        editButton.removeClass('editing');
        var editInput = cancelButton.prev('.save-button').prev('.editable').find('.edit-input');
        var originalValue = editInput.data('original');

        // Restore the original text and remove input and buttons
        editInput.closest('.editable').text(originalValue);
        cancelButton.prev('.save-button').remove();
        cancelButton.remove();
    });
    j('body').on('click', '.save-button', function (e) {
        e.preventDefault();
        var saveButton = j(this);
        var editInput = saveButton.prev('.editable').find('.edit-input');
        var newValue = editInput.val();
        var editButton = saveButton.prev('.editable');
    
        var fileName = editButton.data("file-name");
        var folderPath = editButton.data("folder-path");
        var saveUrl = editButton.data("url");
    
        if (newValue) {
            j.ajax({
                url: saveUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    form_key: FORM_KEY,
                    fileName: fileName,
                    folderPath: folderPath,
                    newValue: newValue
                },
                success: function (response) {
                    console.log("Data Saved successfully:", response);
                    if (response['success']) {
                        editButton.text(newValue);
                        editButton.data("file-name", newValue);
                        editButton.removeClass('editing');
                        
                        var deleteButton = saveButton.closest('tr').find('td:last').find('#delete_file');
                        deleteButton.data("file-path", folderPath + '//' + newValue);

                        var downloadButton = saveButton.closest('tr').find('td:last').find('a');
                        var filePathPattern = /file_path\/[^\/]+/;
                        var fileNamePattern = /file_name\/[^\/]+/;
                        var newFilePath = folderPath + '//' + newValue;
                        encodeNewFilePath = btoa(newFilePath);
                        encodeNewValue = btoa(newValue);
                        var downloadUrl = downloadButton.attr('href');
                        downloadUrl = downloadUrl.replace(filePathPattern, 'file_path/' + encodeNewFilePath);
                        downloadUrl = downloadUrl.replace(fileNamePattern, 'file_name/' + encodeNewValue);
                        downloadButton.attr('href', downloadUrl);
    
                        saveButton.next('.cancel-button').remove();
                        saveButton.remove();
                        alert(response['message']);
                    } else {
                        alert(response['message']);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error Saving data:", error);
                }
            });
        } else {
            alert('File Name is required ...');
        }
    });
    
});
