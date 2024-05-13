var j = jQuery.noConflict();

j(document).ready(function () {
    var reason = {};
    var isEnabled = false;

    j("body").on("click", ".edit-row", function (e) {
        e.preventDefault();

        var editButton = j(this);
        var editUrl = editButton.data("url");
        var itemId = editButton.data("repricer-id");
        var formKey = editButton.data("form-key");
        reason = editButton.data("reason");

        var className = "editable-" + itemId;
        var selector = "td." + className;

        var competitor_checkboxes = j(selector);

        competitor_checkboxes.each(function () {
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

        competitor_checkboxes.first().find(".edit-input").focus();
    });

    j("body").on("click", ".save-button", function (e) {
        e.preventDefault();
        var saveButton = j(this);
        var editUrl = saveButton.data("url");
        var itemId = saveButton.data("repricer-id");
        var formKey = saveButton.data("form-key");

        var className = "editable-" + itemId;
        var selector = "td." + className;

        var competitor_checkboxes = j(selector);
        var editedData = {};

        competitor_checkboxes.each(function () {
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

        competitor_checkboxes.removeAttr("contenteditable");
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

        var competitor_checkboxes = j(selector);

        competitor_checkboxes.each(function () {
            var field = j(this).data("field");
            if (field === "reason") {
                var originalValue = j(this).find('select').children('option:selected').data('original');
                j(this).text(originalValue);
            } else {
                var originalValue = j(this).find('.edit-input').data('original');
                j(this).text(originalValue);
            }
        });

        competitor_checkboxes.removeAttr("contenteditable");
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


    j(".pc_combine").hide();
    j("[name='massaction']").hide();
    j(".massaction-checkbox").hide();
    j(".massaction-competitor").hide();
    j(".massaction").hide();
    j(".headings th:first-child, .filter th:first-child, .a-center").hide();
    j("body").on("click", ".enable_mass_update", function (e) {
        e.preventDefault();
        var button = j(this);
        isEnabled = !isEnabled;
        if (isEnabled) {
            j(".pc_combine").show();
            j("[name='massaction']").show();
            j(".massaction-checkbox").show();
            j(".massaction-competitor").show();
            j(".massaction").show();
            j(".headings th:first-child, .filter th:first-child, .a-center").show();
            button.text("Disable Mass Action")
        } else {
            j(".pc_combine").hide();
            j("[name='massaction']").hide();
            j(".massaction-checkbox").hide();
            j(".massaction-competitor").hide();
            j(".massaction").hide();
            j(".headings th:first-child, .filter th:first-child, .a-center").hide();
            button.text("Enable Mass Action");
            matchingGrid_massactionJsObject.unselectAll()
        }
    });

    j("body").on("change", ".massaction-checkbox", function (e) {
        var isChecked = j(this).prop("checked");

        var row = j(this).closest("tr");
        var checkboxesToCheck = row.find('.competitor-checkbox');
        checkboxesToCheck.prop("checked", isChecked);

        // Check if all "competitor-checkbox" checkboxes are unchecked, then uncheck the "massaction-checkbox"
        if (!isChecked && checkboxesToCheck.filter(':checked').length === 0) {
            row.find('.massaction-checkbox').prop("checked", false);
        }
    });

    j("body").on("change", ".competitor-checkbox", function (e) {
        var isChecked = j(this).prop("checked");
        var row = j(this).closest("tr").parent().closest("tr");
        var checkboxToCheck = row.find('.massaction-checkbox');
        checkboxToCheck.prop("checked", isChecked);

        // Check if there are any "massaction-checkboxes" checkboxes checked
        var anyChecked = row.find('.competitor-checkbox:checked').length > 0;
        checkboxToCheck.prop("checked", anyChecked);
    });


});

varienGridMassaction.prototype.findCheckbox = function (evt) {
    if (['a', 'input', 'select'].indexOf(Event.element(evt).tagName.toLowerCase()) !== -1) {
        return false;
    }
    checkbox = false;
    Event.findElement(evt, 'tr').select('.competitor-checkbox,.massaction-checkbox').each(function (element) {
        if (element.isMassactionCheckbox) {
            checkbox = element;
        }
    }.bind(this));
    return checkbox;
},

    varienGridMassaction.prototype.getCheckboxes = function () {
        var result = [];
        this.grid.rows.each(function (row) {
            var checkboxes = row.select('.competitor-checkbox, .massaction-checkbox');
            checkboxes.each(function (checkbox) {
                result.push(checkbox);
            });
        });
        return result;
    },
    varienGridMassaction.prototype.setCheckbox = function (checkbox) {
        if (checkbox.checked) {
            this.checkedString = varienStringArray.add(
                checkbox.value,
                this.checkedString
            );
            console.log(this.checkedString);
        } else {
            let values = checkbox.value;
            const arrvalues = values.split(',');
            for (let i = 0; i < arrvalues.length; i++) {
                // console.log(arrvalues[i]);
                this.checkedString = varienStringArray.remove(
                    arrvalues[i],
                    this.checkedString
                );
            }
        }
        this.updateCount();
    };