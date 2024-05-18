var j = jQuery.noConflict();

j(document).ready(function () {
    var status = [];

    j("body").on("click", ".edit-jalebi", function (e) {
        e.preventDefault();

        var editButton = j(this);
        var editUrl = editButton.data("url");
        var jalebiId = editButton.data("jalebi-id");
        status = editButton.data("status");
        var className = ".editable-" + jalebiId;

        j(className).each(function () {
            var jalebiType = j(this).find(".jalebi_type");
            var jalebiTypeText = jalebiType.text().trim();
            jalebiType.html(
                '<input type="text" class="edit-input" value="' + jalebiTypeText + '" data-original="' + jalebiTypeText + '">'
            );

            var statusRow = j(this).find(".status");
            var statusText = statusRow.text().trim();
            var dropdown = '<select class="status">';
            Object.keys(status).forEach((element) => {
                dropdown +=
                    '<option value="' + element + '" ' + (statusText == status[element] ? "selected" : "") + ' data-original="' + statusText + '">' + status[element] + "</option>";
            });
            dropdown += "</select>";
            statusRow.html(dropdown);

            editButton.hide();
            var saveButton = j(
                '<a href="#" data-url="' + editUrl + '" data-jalebi-id="' + jalebiId + '" class="save-button">Save</a>'
            );
            var cancelButton = j(
                '<a href="#" style="padding-left:5px" data-url="' + editUrl + '" data-jalebi-id="' + jalebiId + '"class="cancel-button">Cancel</a>'
            );
            var buttonContainer = j("<div>")
                .addClass("button-container")
                .append(saveButton, cancelButton);
            // j(className).removeAttr("contenteditable");
            var cell = editButton.closest("td");
            cell.empty().append(buttonContainer);
        });
    });

    j("body").on("click", ".save-button", function (e) {
        e.preventDefault();
        var saveButton = j(this);
        var editUrl = saveButton.data("url");
        var jalebiId = saveButton.data("jalebi-id");
        var formKey = FORM_KEY;
        var className = ".editable-" + jalebiId;

        var editedData = [];

        j(className).each(function () {
            var jalebiType = j(this).find(".jalebi_type");
            var jalebiTypeText = jalebiType.find('.edit-input').val().trim();
            editedData["jalebi_type"] = jalebiTypeText;
            jalebiType.text(jalebiTypeText);

            var statusRow = j(this).find(".status");
            var statusText = statusRow.find("select").val();
            editedData["status"] = statusText;
            statusRow.text(status[statusText]);
        });
        
        j.ajax({
            url: editUrl,
            type: "POST",
            dataType: "json",
            data: {
                "form_key": formKey,
                "jalebi_id": jalebiId,
                "jalebi_type": editedData['jalebi_type'],
                "status": editedData['status'],
            },
            success: function (response) {
                console.log("Data saved successfully:", response);
            },
            error: function (xhr, status, error) {
                console.log(status);
                console.error("Error saving data:", error);
            },
        });
        // j(className).removeAttr("contenteditable");
        let newstatus = JSON.stringify(status);

        var cell = saveButton.closest("td");
        var a = new Element("a");
        a.innerText = "Edit";
        a.setAttribute("href", "#");
        a.setAttribute("class", "edit-jalebi");
        a.setAttribute("data-url", editUrl);
        a.setAttribute("data-jalebi-id", jalebiId);
        a.setAttribute("data-status", newstatus);
        cell.empty().html(a);
    });


    j("body").on("click", ".cancel-button", function (e) {
        e.preventDefault();
        var cancelButton = j(this);
        var editUrl = cancelButton.data("url");
        var jalebiId = cancelButton.data("jalebi-id");

        var className = ".editable-" + jalebiId;

        j(className).each(function () {
            var jalebiType = j(this).find(".jalebi_type");
            var jalebiTypeText = jalebiType.find('.edit-input').data('original');
            jalebiType.text(jalebiTypeText);

            var statusRow = j(this).find(".status");
            var statusText = statusRow.find("select").children('option:selected').data('original');
            statusRow.text(statusText);
        });
        let newstatus = JSON.stringify(status);

        var cell = cancelButton.closest("td");
        var a = new Element("a");
        a.innerText = "Edit";
        a.setAttribute("href", "#");
        a.setAttribute("class", "edit-jalebi");
        a.setAttribute("data-url", editUrl);
        a.setAttribute("data-jalebi-id", jalebiId);
        a.setAttribute("data-status", newstatus);
        cell.empty().html(a);
    });
});
