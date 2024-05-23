varienGrid.prototype.doFilter = function () {
    console.log(123);
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
var j = jQuery.noConflict();
j(document).ready(function () {
    j(".pc_combine, [name='massaction'], .massaction-checkbox, .massaction-competitor, .massaction").hide();
    j(".headings th:first-child, .filter th:first-child, .a-center").hide();
    j("body").on("click", ".enable_seller", function (e) {
        e.preventDefault();
        var button = j(this);
        var buttonText = button.text();
        if (buttonText == "Assign to seller") {
            j(".pc_combine, [name='massaction'], .massaction-checkbox, .massaction-competitor, .massaction").show();
            j(".headings th:first-child, .filter th:first-child, .a-center").show();
            button.text("Disable Assign to seller")
        } else {
            j(".pc_combine, [name='massaction'], .massaction-checkbox, .massaction-competitor, .massaction").hide();
            j(".headings th:first-child, .filter th:first-child, .a-center").hide();
            button.text("Assign to seller");
            reportGrid_massactionJsObject.unselectAll()
        }
    });
});