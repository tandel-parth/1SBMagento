varienGrid.prototype.doFilter = function () {
    var filters = $$('#' + this.containerId + ' .filter input', '#' + this.containerId + ' .filter select');
    var elements = [];
    filters.push(seller)
    for (var i in filters) {
        if (filters[i].value && filters[i].value.length) {
            elements.push(filters[i]);
        }
    }
    if (!this.doFilterCallback || (this.doFilterCallback && this.doFilterCallback())) {
        this.reload(this.addVarToUrl(this.filterVar, encode_base64(Form.serializeElements(elements))));
    }
};
varienGrid.prototype.resetFilter = function () {
    this.reload(this.addVarToUrl(this.filterVar, ''));
    j('#seller').prop('selectedIndex', 0);
};
var j = jQuery.noConflict();
j(document).ready(function () {
    j("[name='massaction'], .massaction-checkbox, .massaction-competitor, .massaction").hide();
    j(".headings th:first-child, .filter th:first-child, .a-center").hide();
    j("body").on("click", ".enable_seller", function (e) {
        e.preventDefault();
        var button = j(this);
        var buttonText = button.text();
        if (buttonText == "Assign to seller") {
            j("[name='massaction'], .massaction-checkbox, .massaction-competitor, .massaction").show();
            j(".headings th:first-child, .filter th:first-child, .a-center").show();
            button.text("Disable Assign to seller")
        } else {
            j("[name='massaction'], .massaction-checkbox, .massaction-competitor, .massaction").hide();
            j(".headings th:first-child, .filter th:first-child, .a-center").hide();
            button.text("Assign to seller");
            reportGrid_massactionJsObject.unselectAll()
        }
    });
});