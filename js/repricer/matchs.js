var j = jQuery.noConflict();

j(document).ready(function () {
    var reason = {};
    var isMassActionEnabled = false;

    j("body").on("click", ".edit-row", function (e) {
        e.preventDefault();

        var editButton = j(this);
        var editUrl = editButton.data("url");
        var itemId = editButton.data("repricer-id");
        var formKey = editButton.data("form-key");
        reason = editButton.data("reason");

        var className = "editable-" + itemId;
        var selector = "td." + className;

        var editableCells = j(selector);

        editableCells.each(function () {
            var field = j(this).data("field");
            let originalValue = j(this).text().trim();
            if (field === "reason") {
                if (originalValue !== "No Match") {
                    var dropdown = '<select class="edit-dropdown">';
                    Object.keys(reason).forEach((element) => {
                        dropdown +=
                            '<option value="' + element + '" ' + (originalValue == reason[element] ? "selected" : "") + ' data-original="' + originalValue + '">' + reason[element] + "</option>";
                    });
                    dropdown += "</select>";
                    j(this).html(dropdown);
                } else {
                    //   j(this).text(originalValues[field]);
                    var dropdown = '<select class="edit-dropdown" disabled>';
                    Object.keys(reason).forEach((element) => {
                        dropdown +=
                            '<option value="' + element + '" ' + (originalValue == reason[element] ? "selected" : "") + ' data-original="' + originalValue + '">' + reason[element] + "</option>";
                    });
                    dropdown += "</select>";
                    j(this).html(dropdown);
                }
            } else {
                j(this).html(
                    '<input type="text" class="edit-input" value="' + originalValue + '" data-original="' + originalValue + '">'
                );
            }
        });

        editButton.hide();

        var saveButton = j(
            '<a  href="#" data-url="' + editUrl + '" data-reason="' + reason + '" data-repricer-id="' + itemId + '" data-form-key="' + formKey + '" class="save-button">Save</a>'
        );
        var cancelButton = j(
            '<a href="#" style="padding-left:5px" data-url="' + editUrl + '" data-reason="' + reason + '" data-repricer-id="' + itemId + '" data-form-key="' + formKey + '"  class="cancel-button">Cancel</a>'
        );
        var buttonContainer = j("<div>")
            .addClass("button-container")
            .append(saveButton, cancelButton);

        var cell = editButton.closest("td");
        cell.empty().append(buttonContainer);

        editableCells.first().find(".edit-input").focus();
    });

    j("body").on("click", ".save-button", function (e) {
        e.preventDefault();
        var saveButton = j(this);
        var editUrl = saveButton.data("url");
        var itemId = saveButton.data("repricer-id");
        var formKey = saveButton.data("form-key");

        var className = "editable-" + itemId;
        var selector = "td." + className;

        var editableCells = j(selector);
        var editedData = {};

        editableCells.each(function () {
            var field = j(this).data("field");
            var value;
            if (field === "reason") {
                value = j(this).find("select").val();
                editedData[field] = value;
            } else {
                value = j(this).find(".edit-input").val().trim();
                editedData[field] = value;
            }

            if (field === "reason") {
                j(this).text(reason[value]);
            } else {
                j(this).text(value);
            }
        });

        j.ajax({
            url: editUrl,
            type: "POST",
            dataType: "json",
            data: {
                form_key: formKey,
                itemId: itemId,
                editedData: editedData,
            },
            success: function (response) {
                console.log("Data saved successfully:", response);
            },
            error: function (xhr, status, error) {
                console.log(status);
                console.error("Error saving data:", error);
            },
        });

        editableCells.removeAttr("contenteditable");
        let newreason = JSON.stringify(reason);

        var cell = saveButton.closest("td");
        var a = new Element("a");
        a.innerText = "Edit";
        a.setAttribute("href", "#");
        a.setAttribute("class", "edit-row");
        a.setAttribute("data-url", editUrl);
        a.setAttribute("data-repricer-id", itemId);
        a.setAttribute("data-reason", newreason);
        a.setAttribute("data-form-key", formKey);
        cell.empty().html(a);
    });

    j("body").on("click", ".cancel-button", function (e) {
        e.preventDefault();
        var cancelButton = j(this);
        var editUrl = cancelButton.data("url");
        var itemId = cancelButton.data("repricer-id");
        var formKey = cancelButton.data("form-key")

        var className = "editable-" + itemId;
        var selector = "td." + className;

        var editableCells = j(selector);

        editableCells.each(function () {
            var field = j(this).data("field");
            if (field === "reason") {
                var originalValue = j(this).find('select').children('option:selected').data('original');
                j(this).text(originalValue);
            } else {
                var originalValue = j(this).find('.edit-input').data('original');
                j(this).text(originalValue);
            }
        });

        editableCells.removeAttr("contenteditable");
        let newreason = JSON.stringify(reason);

        var cell = cancelButton.closest("td");
        var a = new Element("a");
        a.innerText = "Edit";
        a.setAttribute("href", "#");
        a.setAttribute("class", "edit-row");
        a.setAttribute("data-url", editUrl);
        a.setAttribute("data-repricer-id", itemId);
        a.setAttribute("data-reason", newreason);
        a.setAttribute("data-form-key", formKey);
        cell.empty().html(a);
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
            // j(".massaction-checkbox").prop("checked", false);
            var checkedCheckboxes = j("input[type='checkbox'][class='massaction-checkbox']:checked");
            checkedCheckboxes.each(function () {
                var currentCheckbox = j(this);
                if (currentCheckbox.is(":checked")) {
                    currentCheckbox.prop("checked", false);
                    currentCheckbox.trigger("change");
                    console.log(currentCheckbox);
                }
            });
        }

    });
    var massActionCheckbox = j('.massaction-checkbox');

    massActionCheckbox.on("change", function () {
        var repricerId = j(this).val();
        var rowCheckboxes = j("input[type='checkbox'][name='massaction'][id='" + repricerId + "']");
        var isChecked = j(this).is(":checked");

        // var massActionProCheckbox = j("input[type='checkbox'][class='massaction-checkbox'][value='" + repricerId + "']");
        // if(isChecked){
        //     massActionProCheckbox.trigger('click');
        //     massActionProCheckbox.prop("checked", true);
        // }
        console.log(rowCheckboxes);
        rowCheckboxes.each(function () {
            var currentCheckbox = j(this);
            var isCurrentlyChecked = currentCheckbox.is(":checked");

            if (isChecked && !isCurrentlyChecked) {
                // currentCheckbox.prop("checked", true);   
                currentCheckbox.trigger('click');
            } else if (!isChecked && isCurrentlyChecked) {
                // currentCheckbox.prop("checked", false);
                currentCheckbox.trigger('click');
            }
        });
        // currentCheckbox.trigger('click');
    });

    j("input[type='checkbox'][name='massaction']").on("change", function () {
        var repricerId = j(this).attr("id");
        var productCheckboxes = j("input[type='checkbox'][name='massaction'][id='" + repricerId + "']");
        var allChecked = false;
        console.log(productCheckboxes);
        if (productCheckboxes.filter(":checked").length !== 0) {
            allChecked = true;
        }
        else {
            allChecked = false;
        }
        var massActionProductCheckbox = j("input[type='checkbox'][class='massaction-checkbox'][value='" + repricerId + "']");
        massActionProductCheckbox.prop("checked", allChecked);
    });

});