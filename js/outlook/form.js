var j = jQuery.noConflict();
var eventManager = Class.create();

var lastGroupId = 0; // Global variable to keep track of group_id

eventManager.prototype = {
    initialize: function (options) {
        this.containerId = options.container_id;
        this.data = options.data;
        this.tableIndex = 0;
        this.tables = {};
        this.loadContainer();
        this.renderExistingTables();
    },

    loadContainer: function () {
        var self = this;
        j("#add_button").on("click", function () {
            self.renderTable(self.tableIndex++);
        });
    },

    renderExistingTables: function () {
        for (var key in this.data) {
            if (this.data.hasOwnProperty(key)) {
                this.renderTable(this.tableIndex, this.data[key]);
                this.tableIndex++;
            }
        }
    },

    renderTable: function (tableIndex, tableData) {
        var self = this;
        var table = this.createElement("table");
        var tableId = 'table_' + tableIndex; // Unique ID for each table
        table.setAttribute('id', tableId);
        var groupId = ++lastGroupId; // Generate unique group_id for this table

        // Store the group_id for this table
        this.tables[tableIndex] = {
            rowIndex: 0,
            eventCell: null,
            deleteTableCell: null,
            groupId: groupId
        };

        var tableHeader = [
            "Field",
            "Operators",
            "Value",
            "Event",
            "Delete",
        ];
        var dataType = ["to_email", "from_email"];
        var conditionOperator = ["=", "like", '%like%'];

        var tr1 = document.createElement("tr");
        for (var i = 0; i < tableHeader.length; i++) {
            var th = document.createElement("th");
            th.innerText = tableHeader[i];
            tr1.appendChild(th);
        }
        table.appendChild(tr1);

        if (tableData) {
            tableData.forEach((row, rowIndex) => {
                // Pass the groupId to addRow
                self.addRow(table, tableIndex, rowIndex, row, dataType, conditionOperator, groupId);
            });
        } else {
            // Pass the groupId to addRow
            self.addRow(table, tableIndex, 0, null, dataType, conditionOperator, groupId);
        }

        var tableContainer = document.getElementById(this.containerId);
        tableContainer.appendChild(table);
    },

    addRow: function (table, tableIndex, rowIndex, rowData, dataType, conditionOperator, groupId) {
        var self = this;
        var tr = document.createElement("tr");
        if (rowData) self.tables[tableIndex].rowIndex = rowIndex;

        // Create hidden input for group_id with the provided groupId
        var hiddenGroupIdInput = document.createElement("input");
        hiddenGroupIdInput.type = "hidden";
        hiddenGroupIdInput.name = `events[${tableIndex}][rows][${rowIndex}][group_id]`;
        hiddenGroupIdInput.value = groupId;
        tr.appendChild(hiddenGroupIdInput);

        // Create hidden input for event_id
        var hiddenEventIdInput = document.createElement("input");
        hiddenEventIdInput.type = "hidden";
        hiddenEventIdInput.name = `events[${tableIndex}][rows][${rowIndex}][event_id]`;

        // If rowData is available and contains event_id, populate it; otherwise set it to null
        if (rowData && rowData.event_id !== undefined) {
            hiddenEventIdInput.value = rowData.event_id;
        } else {
            hiddenEventIdInput.value = null;
        }
        tr.appendChild(hiddenEventIdInput);

        var dataTypeSelect = this.createDropDown(dataType, `events[${tableIndex}][rows][${rowIndex}][condition_on]`);
        if (rowData) dataTypeSelect.value = rowData.condition_on;

        var conditionOperatorSelect = this.createDropDown(conditionOperator, `events[${tableIndex}][rows][${rowIndex}][operator]`);
        if (rowData) conditionOperatorSelect.value = rowData.operator;

        var td1 = document.createElement("td");
        td1.appendChild(dataTypeSelect);

        var td2 = document.createElement("td");
        td2.appendChild(conditionOperatorSelect);

        var td3 = document.createElement("td");
        var input1 = document.createElement("input");
        input1.type = "text";
        input1.required = true;
        input1.setAttribute('name', `events[${tableIndex}][rows][${rowIndex}][value]`);
        if (rowData) input1.value = rowData.value;

        td3.appendChild(input1);

        var deleteRowButton = document.createElement("button");
        deleteRowButton.innerText = "Delete Row";
        deleteRowButton.setAttribute('id', 'deleteRowButton');
        deleteRowButton.onclick = function (event) {
            event.preventDefault();

            // Get the next row
            var nextRow = tr.nextSibling;

            // If next row exists
            var deleteRowButton = tr.querySelector("button#deleteTableButton");
            var eventRowButton = tr.querySelector(`input[name='events[${tableIndex}][event]`);
            var rowCount = document.querySelectorAll(`#table_${tableIndex} tr`).length - 1;
            console.log(rowCount);
            if (deleteRowButton && eventRowButton) {
                if (nextRow) {
                    var td4 = document.createElement("td");
                    td4.appendChild(eventRowButton);
                    td4.rowSpan = rowCount;
                    nextRow.appendChild(td4);
                    var td5 = document.createElement("td");
                    td5.appendChild(deleteRowButton);
                    td5.rowSpan = rowCount;
                    nextRow.appendChild(td5);
                }
                rowCount--;
            }
            if (!(rowCount)) {
                table.remove();
            } else {
                tr.remove();
                self.addButtonToLastRow(table, tableIndex, groupId);
            }

            self.updateRowSpan(tableIndex);
        };


        td3.appendChild(deleteRowButton);

        var td4 = document.createElement("td");
        if (this.tables[tableIndex].eventCell === null) {
            var input2 = document.createElement("input");
            input2.type = "text";
            input2.setAttribute('name', `events[${tableIndex}][event]`);
            input2.required = true;
            if (rowData) input2.value = rowData.event;
            td4.appendChild(input2);
            td4.rowSpan = 1;  // Initial rowspan
            this.tables[tableIndex].eventCell = td4;
        } else {
            this.tables[tableIndex].eventCell.rowSpan += 1;
        }

        var td5 = document.createElement("td");
        if (this.tables[tableIndex].deleteTableCell === null) {
            var deleteTableButton = document.createElement("button");
            deleteTableButton.innerText = "Delete Table";
            deleteTableButton.setAttribute('id', 'deleteTableButton');
            deleteTableButton.onclick = function (event) {
                event.preventDefault();
                table.remove();
            };
            td5.appendChild(deleteTableButton);
            td5.rowSpan = 1;  // Initial rowspan
            this.tables[tableIndex].deleteTableCell = td5;
        } else {
            this.tables[tableIndex].deleteTableCell.rowSpan += 1;
        }

        tr.appendChild(td1);
        tr.appendChild(td2);
        tr.appendChild(td3);

        if (this.tables[tableIndex].eventCell.parentElement === null) {
            tr.appendChild(td4);
        }
        if (this.tables[tableIndex].deleteTableCell.parentElement === null) {
            tr.appendChild(td5);
        }

        table.appendChild(tr);
        this.addButtonToLastRow(table, tableIndex, groupId);
    },

    createElement: function (tagName) {
        return document.createElement(tagName);
    },

    createDropDown: function (options, name) {
        var select = document.createElement("select");
        select.setAttribute("name", name);
        for (var i = 0; i < options.length; i++) {
            var option = document.createElement("option");
            option.value = options[i];
            option.text = options[i];
            select.appendChild(option);
        }
        return select;
    },

    cloneRow: function (row, tableIndex, groupId) {
        var self = this;
        var rowIndex = self.tables[tableIndex].rowIndex;
        rowIndex++;

        var newRow = this.createElement("tr");

        // Create hidden input for group_id
        var hiddenGroupIdInput = document.createElement("input");
        hiddenGroupIdInput.type = "hidden";
        hiddenGroupIdInput.name = `events[${tableIndex}][rows][${rowIndex}][group_id]`;
        hiddenGroupIdInput.value = groupId;
        newRow.appendChild(hiddenGroupIdInput);

        for (var i = 0; i < 3; i++) {
            var newCell = row.cells[i].cloneNode(true);
            var inputs = newCell.getElementsByTagName('input');
            for (var j = 0; j < inputs.length; j++) {
                inputs[j].name = inputs[j].name.replace(`[rows][${self.tables[tableIndex].rowIndex}]`, `[rows][${rowIndex}]`);
                inputs[j].value = ""; // Clear the value of the cloned input
            }
            var selects = newCell.getElementsByTagName('select');
            for (var j = 0; j < selects.length; j++) {
                selects[j].name = selects[j].name.replace(`[rows][${self.tables[tableIndex].rowIndex}]`, `[rows][${rowIndex}]`);
                selects[j].selectedIndex = 0; // Reset the select to its default value
            }
            newRow.appendChild(newCell);
        }

        var deleteRowButton = newRow.querySelector("button#deleteRowButton");
        deleteRowButton.onclick = function (event) {
            event.preventDefault();
            var rowCount = document.querySelectorAll(`#table_${tableIndex} tr`).length - 1;
            var deleteRowButton = newRow.querySelector("button#deleteTableButton");
            var eventRowButton = newRow.querySelector(`input[name='events[${tableIndex}][event]`);
            if (deleteRowButton && eventRowButton) {
                var nextRow = newRow.nextSibling;
                if (nextRow) {
                    var td4 = document.createElement("td");
                    td4.appendChild(eventRowButton);
                    td4.rowSpan = rowCount;
                    nextRow.appendChild(td4);
                    var td5 = document.createElement("td");
                    td5.appendChild(deleteRowButton);
                    td5.rowSpan = rowCount;
                    nextRow.appendChild(td5);
                }
                rowCount--;
            }
            if (!(rowCount)) {
                newRow.parentNode.remove();
            } else {
                newRow.remove();
                self.addButtonToLastRow(row.parentNode, tableIndex, groupId); // Ensure add button is present only in the last row
            }
            self.updateRowSpan(tableIndex);
        };

        row.parentNode.insertBefore(newRow, row.nextSibling);

        self.updateRowSpan(tableIndex);
        self.addButtonToLastRow(row.parentNode, tableIndex, groupId);
    },

    addButtonToLastRow: function (table, tableIndex, groupId) {
        // Remove all existing add buttons
        j(table).find('button#addButton').remove();
        var self = this;

        // Add the add button to the last row
        var lastRow = table.rows[table.rows.length - 1];
        var addButton = document.createElement("button");
        addButton.innerText = "Add";
        addButton.setAttribute('id', 'addButton');
        addButton.onclick = function (event) {
            event.preventDefault();
            self.cloneRow(lastRow, tableIndex, groupId); // Pass groupId to cloneRow
        };
        lastRow.cells[2].appendChild(addButton);
    },

    updateRowSpan: function (tableIndex) {
        var eventCell = this.tables[tableIndex].eventCell;
        var deleteTableCell = this.tables[tableIndex].deleteTableCell;
        if (eventCell && deleteTableCell) {
            var rowCount = document.querySelectorAll(`#table_${tableIndex} tr`).length - 1;  // Subtract header row
            eventCell.rowSpan = rowCount;
            deleteTableCell.rowSpan = rowCount;
        }
    }
};