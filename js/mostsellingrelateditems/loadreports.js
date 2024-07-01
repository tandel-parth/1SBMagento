var j = jQuery.noConflict();
function filterChanges(changes, url) {
    const selectElement = document.querySelector('.product-load');
    const selectValue = selectElement.value;
    new Ajax.Request(url, {
        method: 'post',
        dataType: "json",
        parameters: { id: selectValue , config: changes.dataset.related},
        onSuccess: function (response) {
            var container = document.querySelector('#product-container');
            container.innerHTML = response.responseText;
            var relatedContainer = document.querySelector('#report-container');
            relatedContainer.innerHTML = '';

        },
        onFailure: function () {
            alert('Failed to apply Save.');
        }
    });
}

function showData(button) {
    const url = button.dataset.url;
    const value = button.dataset.id;
    new Ajax.Request(url, {
        method: 'post',
        dataType: "json",
        parameters: { id: value },
        onSuccess: function (response) {
            var container = document.querySelector('#report-container');
            container.innerHTML = response.responseText;
        },
        onFailure: function () {
            alert('Failed to apply Save.');
        }
    });
}