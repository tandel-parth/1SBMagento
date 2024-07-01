var j = jQuery.noConflict();

j(document).ready(function () {
    function clearFormFields() {
        CKEDITOR.instances['description'].setData('');
    }
    j('.editable').click(function () {
        var editableField = j(this);
        if (!editableField.hasClass('editing')) {
            var fieldData = editableField.data('field');
            var urlAction = editableField.data('url');
            var currentValue = editableField.html().trim();
            var Options = editableField.data('array');

            // Create input field
            var inputField;
            if (fieldData === 'descreption') {
                inputField = j('<textarea class="edit-input">' + currentValue + '</textarea>');
                editableField.empty().append(inputField);
                // Ensure the textarea has a unique ID  
                var uniqueId = 'editor-' + Math.random().toString(36).substr(2, 9);
                inputField.attr('id', uniqueId);
                // Initialize CKEditor on the textarea after it is appended
                CKEDITOR.replace(uniqueId);

                // Attach onchange event to CKEditor
                CKEDITOR.instances[uniqueId].on('change', function () {
                    handleSave(editableField, fieldData, uniqueId, urlAction, currentValue);
                });
            } else if (fieldData === 'status' || fieldData === 'assign_to' || fieldData === 'assign_by') {
                inputField = j('<select class="edit-input"></select>');
                Options.forEach(function (data) {
                    var option = j('<option></option>').attr('value', data.id).text(data.value);
                    if (data.value === currentValue) {
                        option.attr('selected', 'selected');
                    }
                    inputField.append(option);
                });
                editableField.empty().append(inputField);

                // Attach onchange event to select dropdown
                inputField.change(function () {
                    if (Options) {
                        handleSave(editableField, fieldData, inputField, urlAction, currentValue, Options);
                    } else {
                        handleSave(editableField, fieldData, inputField, urlAction, currentValue);
                    }
                });
            } else {
                inputField = j('<input type="text" class="edit-input" value="' + currentValue + '"/>'); // Use input for other fields
                editableField.empty().append(inputField);

                // Attach onchange event to input field
                inputField.change(function () {
                    handleSave(editableField, fieldData, inputField, urlAction, currentValue);
                });
            }

            // Create cancel button
            var cancelButton = j('<button class="cancel btn">Cancel</button>');
            var buttonContainer = j('<div class="edit-actions"></div>').append(cancelButton);
            editableField.parent().append(buttonContainer);
            editableField.addClass('editing');

            // Handle cancel button click
            cancelButton.click(function () {
                if (fieldData === 'descreption') {
                    if (CKEDITOR.instances[uniqueId]) {
                        CKEDITOR.instances[uniqueId].destroy();
                    }
                }
                editableField.removeClass('editing');
                editableField.html(currentValue);
                buttonContainer.remove();
            });
        }
    });

    function handleSave(editableField, fieldData, inputFieldOrUniqueId, urlAction, currentValue, Options = {}) {
        var newValue;
        if (fieldData === 'descreption') {
            newValue = CKEDITOR.instances[inputFieldOrUniqueId].getData().trim();
            CKEDITOR.instances[inputFieldOrUniqueId].destroy();
        } else {
            newValue = j(inputFieldOrUniqueId).val().trim();
        }
        var formKey = FORM_KEY;
        // Send AJAX request to save new value
        j.ajax({
            url: urlAction,
            type: "POST",
            dataType: "json",
            data: {
                field: fieldData,
                value: newValue,
                form_key: formKey,
            },
            success: function (response) {
                console.log("Data Saved successfully:", response);
                if (response['success']) {
                    editableField.removeClass('editing');
                    if (Array.isArray(Options) && Options.length > 0) {
                        var option = Options.find(function (opt) {
                            return opt.id === newValue;
                        });

                        if (option) {
                            editableField.html(option.value);
                        } else {
                            editableField.html(newValue);
                        }
                    } else {
                        editableField.html(newValue);
                    }
                    editableField.next('.edit-actions').remove(); // Remove the button container
                    alert(response['message']);
                }
            },
            error: function (xhr, status, error) {
                console.log("Error response from server:", xhr.responseText);
                console.log("Status:", status);
                console.log("Error:", error);
                // Handle error response
                if (fieldData === 'descreption') {
                    if (CKEDITOR.instances[inputFieldOrUniqueId]) {
                        CKEDITOR.instances[inputFieldOrUniqueId].destroy();
                    }
                }
                editableField.removeClass('editing');
                editableField.html(currentValue);
                editableField.next('.edit-actions').remove(); // Remove the button container
                alert('Error saving data');
            }
        });
    }

});


document.addEventListener("DOMContentLoaded", function () {
    initializeCKEditor(document.querySelector('.description'));
    const submitButton = document.querySelector('.submit-comment-btn');
    if (submitButton) {
        submitButton.addEventListener('click', function () {
            submitComment();
        });
    }
});

function initializeCKEditor(textarea) {
    if (typeof CKEDITOR !== 'undefined') {
        if (textarea) {
            if (!textarea.id) {
                textarea.id = `editor-${Date.now()}`;
            }
            CKEDITOR.replace(textarea.id);
        }
    } else {
        console.error('CKEditor is not loaded.');
    }
}

function submitComment() {
    const textarea = document.querySelector('.description');
    const editorInstance = CKEDITOR.instances[textarea.id];
    const editorData = editorInstance.getData().trim();
    const ticketId = textarea.dataset.ticket;
    const url = textarea.dataset.url;

    if (editorData === "") {
        alert("Please enter a comment");
        return;
    }

    const formData = {
        ticket_id: ticketId,
        description: editorData
    };

    // Perform the AJAX request
    new Ajax.Request(url, {
        method: 'post',
        dataType: "json",
        parameters: { datasave: JSON.stringify(formData) },
        onSuccess: function (response) {
            const responseData = JSON.parse(response.responseText);
            const comment = document.querySelector('.container-box');
            comment.remove();
            loadBlock(responseData.url, ticketId);
        },
        onFailure: function () {
            alert('Failed to apply Save.');
        }
    });
}

function submitReply(submitButton) {
    const replyContainer = submitButton.closest('.reply-container');
    const textarea = replyContainer.querySelector('.description');
    const editorInstance = CKEDITOR.instances[textarea.id];
    const editorData = editorInstance.getData().trim();
    const ticketId = textarea.dataset.ticket;
    const url = textarea.dataset.url;
    const parentId = textarea.dataset.parentId;
    const level = parseInt(textarea.dataset.level, 10);
    console.log(level);

    if (editorData === "") {
        alert("Please enter a reply");
        return;
    }

    const formData = {
        ticket_id: ticketId,
        description: editorData,
        parent_id: parentId,
        level: level
    };

    new Ajax.Request(url, {
        method: 'post',
        dataType: "json",
        parameters: { datasave: JSON.stringify(formData) },
        onSuccess: function (response) {
            const responseData = JSON.parse(response.responseText);
            editorInstance.destroy();
            replyContainer.innerHTML = '';

            const replyText = document.createElement('span');
            replyText.classList.add('comment-content');
            replyText.dataset.commentId = responseData.data.comment_id;
            replyText.dataset.parentId = parentId;
            replyText.dataset.level = level;
            replyText.textContent = editorData;
            replyContainer.appendChild(replyText);

            const parentSpan = document.querySelector(`[data-comment-id="${parentId}"]`);
            if (parentSpan) {
                const trCurrunt = parentSpan.closest('tr');
                const completeButton = trCurrunt.querySelector('.complete-button');
                completeButton.remove();
            }
            if (parentId != 0) {
                const lock = document.querySelector('.lock-container');
                lock.show();
                if (level !== 1) {
                    const tdLock = lock.querySelector('.colspan-lock');
                    tdLock.show();
                }
            } else {
                const replyButton = document.createElement('button');
                replyButton.classList.add('add-reply-btn');
                replyButton.textContent = 'Reply';
                replyButton.dataset.commentId = responseData.data.comment_id;
                replyButton.dataset.parentId = parentId;
                replyButton.dataset.url = url;
                replyButton.dataset.ticket = ticketId;
                replyButton.addEventListener('click', function () {
                    addReply(replyButton);
                });
                replyContainer.appendChild(replyButton);

                const completeButton = document.createElement('button');
                completeButton.classList.add('complete-button');
                completeButton.textContent = 'Complete';
                completeButton.dataset.commentId = responseData.data.comment_id;
                const updateurl = url.replace("saveComment", "updateStatus");
                console.log(updateurl);


                completeButton.dataset.url = updateurl;
                completeButton.addEventListener('click', function () {
                    completeComment(completeButton);
                });
                replyContainer.appendChild(completeButton);
            }


            updateRowSpans(replyContainer.closest('tbody'));
        },
        onFailure: function () {
            alert('Failed to apply Save.');
        }
    });
}

function addReply(button) {
    const tr = button.closest('tr');
    const tdAddReply = tr.querySelector('.comment-add');
    if (tdAddReply) {
        tdAddReply.dataset.commentId = button.dataset.commentId;
    }
    const parentId = button.dataset.commentId;
    const ticketId = button.dataset.ticket;
    const url = button.dataset.url;
    const parentLevel = getCommentLevel(parentId);

    const newTr = document.createElement('tr');
    const tdTextarea = document.createElement('td');
    tdTextarea.classList.add('reply-container');
    const textarea = document.createElement('textarea');
    textarea.classList.add('description');
    textarea.placeholder = 'Enter your reply';
    textarea.dataset.ticket = ticketId;
    textarea.dataset.parentId = parentId;
    textarea.dataset.url = url;
    textarea.dataset.level = parentLevel + 1;
    textarea.id = 'editor-' + new Date().getTime();

    const submitButton = document.createElement('button');
    submitButton.textContent = 'Submit';
    submitButton.classList.add('submit-comment-btn');
    submitButton.addEventListener('click', function () {
        submitReply(submitButton);
    });
    tdTextarea.appendChild(textarea);
    tdTextarea.appendChild(submitButton);

    newTr.appendChild(tdTextarea);

    const allRows = document.querySelectorAll(`[data-parent-id="${parentId}"]`);
    const lastRow = allRows[allRows.length - 1];
    const lastTr = lastRow ? lastRow.closest('tr') : tr;

    lastTr.parentNode.insertBefore(newTr, lastTr.nextSibling);

    updateRowSpans(tr.parentNode);

    initializeCKEditor(textarea);
}

function getCommentLevel(commentId) {
    const comments = document.querySelectorAll(`span[data-comment-id="${commentId}"]`);
    let level = 0;
    comments.forEach(comment => {
        level = parseInt(comment.dataset.level, 10) || 0;
    });
    return level;
}


function updateRowSpans(tbody) {
    const allRows = Array.from(tbody.querySelectorAll('tr'));

    // Reset the rowSpan for all cells to avoid cumulative addition
    allRows.forEach(row => {
        const tds = row.querySelectorAll('td');
        tds.forEach(td => {
            if (td) {
                td.rowSpan = 1;
            }
        });
    });
    // Function to find parent rows and update their rowSpan
    function updateParentRowSpan(tr) {
        const parentId = tr.querySelector('textarea')?.dataset.parentId || tr.querySelector('span')?.dataset.parentId;
        if (parentId) {
            let parentRow = null;
            for (let row of allRows) {
                const rowTextarea = row.querySelector('textarea');
                const rowSpan = row.querySelector('span');
                if ((rowTextarea && rowTextarea.dataset.commentId === parentId) || (rowSpan && rowSpan.dataset.commentId === parentId)) {
                    parentRow = row;
                    break;
                }
            }
            if (parentRow) {
                const parentTds = parentRow.querySelectorAll('td');
                if (parentTds) {
                    parentTds.forEach(parentTd => {
                        parentTd.rowSpan = (parentTd.rowSpan || 1) + 1;
                    });
                    updateParentRowSpan(parentRow); // Recursively update parents
                }
            }
        }
    }

    // Update rowSpan for each row considering parent-child relationship
    for (let i = 1; i < allRows.length; i++) {
        const td = allRows[i].querySelector('td');
        if (td) {
            updateParentRowSpan(allRows[i]);
        }
    }

}

function lockComment(button, url) {
    const allRows = document.querySelectorAll(`[data-level="${button.dataset.level}"]`);
    let hasButtonTag = false;
    let children = [];
    allRows.forEach(row => {
        if (row.tagName === 'SPAN') {
            console.log(row.tagName);
            children.push(row.dataset.commentId);
        }
        const allNewRows = document.querySelectorAll(`[data-parent-id="${row.dataset.commentId}"]`);
        if (allNewRows.length) {
            allNewRows.forEach(row => {
                if (row.tagName === 'TEXTAREA') {
                    hasButtonTag = true;
                }
            });
        } else {
            const trCurrunt = row.closest('tr');
            const completeButton = trCurrunt.querySelector('.complete-button');
            if (completeButton) {
                completeComment(completeButton);
            }
        }
    });
    if (!hasButtonTag) {
        console.log(children); 
        lockSave(button.dataset.url, children);
        loadBlock(url, button.dataset.ticket);
    } else {
        alert('submit form');
        return;
    }
}
function lockSave(url, children) {
    new Ajax.Request(url, {
        method: 'post',
        dataType: "json",
        parameters: { comment_id: JSON.stringify(children) },
        onSuccess: function (response) {
            console.log(response);
        },
        onFailure: function () {
            alert('Failed to apply filters.');
        }
    });

}
function completeComment(button) {
    const commentId = button.dataset.commentId;
    const url = button.dataset.url;
    new Ajax.Request(url, {
        method: 'post',
        dataType: "json",
        parameters: { comment_id: commentId, status: 'complete' },
        onSuccess: function (response) {
            const responseData = JSON.parse(response.responseText);
            loadBlock(responseData.url, responseData.ticketId);
        },
        onFailure: function () {
            alert('Failed to apply filters.');
        }
    });
}

function loadBlock(url, ticketId) {
    new Ajax.Request(url, {
        method: 'post',
        dataType: "json",
        parameters: { id: ticketId },
        onSuccess: function (response) {

            const commentContainer = document.querySelector('.comment-container');
            const tableContainer = document.querySelectorAll('.comment-table');
            tableContainer.forEach(row => {
                row.remove();
            });
            const status = document.querySelector('.container-title');
            status.remove();
            const h1 = document.querySelector('.container-h1');
            h1.remove();
            if (commentContainer) {
                commentContainer.innerHTML = response.responseText;
            }
        },
        onFailure: function () {
            alert('Failed to Load Block.');
        }
    });
}
function addQuestions(button) {
    const tr = button.closest('tr');
    const parentId = '0';
    const ticketId = button.dataset.ticket;
    const url = button.dataset.url;

    const newTr = document.createElement('tr');
    if (button.dataset.level != 0) {
        const tdBlank = document.createElement('td');
        tdBlank.colSpan = button.dataset.level;
        newTr.appendChild(tdBlank);
    }
    const tdTextarea = document.createElement('td');
    tdTextarea.classList.add('reply-container');
    const textarea = document.createElement('textarea');
    textarea.classList.add('description');
    textarea.placeholder = 'Enter your reply';
    textarea.dataset.ticket = ticketId;
    textarea.dataset.parentId = parentId;
    textarea.dataset.url = url;
    textarea.dataset.level = button.dataset.level;
    textarea.id = 'editor-' + new Date().getTime();

    const submitButton = document.createElement('button');
    submitButton.textContent = 'Submit';
    submitButton.classList.add('submit-comment-btn');
    submitButton.addEventListener('click', function () {
        submitReply(submitButton);
    });
    tdTextarea.appendChild(textarea);
    tdTextarea.appendChild(submitButton);

    newTr.appendChild(tdTextarea);

    const lastTr = button.closest('tr');

    lastTr.parentNode.insertBefore(newTr, lastTr);

    initializeCKEditor(textarea);
}

function filterChanges(changes) {
    const url = changes.dataset.url;
    const ticketId = changes.dataset.ticket;
    const value = changes.value;
    new Ajax.Request(url, {
        method: 'post',
        dataType: "json",
        parameters: { status: value, id: ticketId },
        onSuccess: function (response) {
            const commentContainer = document.querySelector('.comment-container');
            const tableContainer = document.querySelectorAll('.comment-table');
            tableContainer.forEach(row => {
                row.remove();
            });
            const comment = document.querySelector('.container-box');
            if (comment) {
                comment.remove();
            }
            const status = document.querySelector('.container-title');
            status.remove();
            const h1 = document.querySelector('.container-h1');
            h1.remove();
            if (commentContainer) {
                commentContainer.innerHTML = response.responseText;
            }

        },
        onFailure: function () {
            alert('Failed to apply Save.');
        }
    });
}