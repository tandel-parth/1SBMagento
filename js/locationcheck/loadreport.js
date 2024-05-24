var j = jQuery.noConflict();
j(document).ready(function () {

    j("body").on("click", ".load_report", function (e) {
        locationCheck = j("#location-check").val();
        productExcluded = j("#product-excluded").val();
        if (productExcluded != 1 && productExcluded != 2) {
            productExcluded = "khali";
        }
        var params = { 'is_location_checked': locationCheck, 'product_excluded_location_check': productExcluded };
        new Ajax.Request('http://127.0.0.1/magento/index.php/admin/loadreport/getData', {
            method: 'POST',
            parameters: params,
            onSuccess: function (responce) {
                var data = JSON.parse(responce.responseText);
                var container = $('report-container');
                container.update("");
                var table = new Element('table', { 'class': 'report-table' });

                var thead = new Element('thead');
                var headerRow = new Element('tr');
                ['Order Id', 'Customer Email', 'is_location_check', 'product_exclude_location_check'].forEach(function (header) {
                    headerRow.insert(new Element('th').update(header));
                });
                thead.insert(headerRow);
                table.insert(thead);

                // Create table body
                var tbody = new Element('tbody');
                data.forEach(function (item) {
                    var row = new Element('tr');
                    var loc_check = (item.is_location_checked == 1) ? 'Yes' : 'No';
                    var prod_check = (item.product_excluded_location_check == 1) ? 'Yes' : 'No';
                    row.insert(new Element('td').update(item.entity_id));
                    row.insert(new Element('td').update(item.customer_email));
                    row.insert(new Element('td').update(loc_check));
                    row.insert(new Element('td').update(prod_check));
                    tbody.insert(row);
                });
                table.insert(tbody);
                container.insert(table);
            }
        });
    });
});