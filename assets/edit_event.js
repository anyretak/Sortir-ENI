function updateEvent() {
    let eventSelect = document.querySelector('#app-js-event');
    let event = eventSelect.dataset.event;
    console.log(event);

    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/ajax',
        data: {data: event},
        success: function (data) {
            console.log(data);
        }
    });

    /*  GET RID OF JQUERY*/
    /*    fetch ( 'http://localhost/sortir-eni/public/ajax', {method:"POST",  body: {data: event} } .then ((console.log(data)));*/
}

window.updateEvent = updateEvent;

function addCity() {
    let cityName = document.getElementById("app-js-city-name").value;
    let cityCode = document.getElementById("app-js-city-code").value;
    let table = document.getElementById("app-city-table").value;

    console.log(cityName + cityCode);

    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/admin/edit_city',
        data: {
            city: cityName,
            code: cityCode
        },
        success: function (data) {
            let rowCount = $('#app-city-table tr').length;
            let index = rowCount - 1;
            let table = document.getElementById("app-city-table");
            let row = table.insertRow(index);
            let cell1 = row.insertCell(0);
            let cell2 = row.insertCell(1);
            let cell3 = row.insertCell(2);
            cell1.innerHTML = cityName;
            cell2.innerHTML = cityCode;
            cell3.innerHTML = "<a href=\"\">Edit</a> <a href=\"\">Remove</a>";
        }
    });
}

window.addCity = addCity;

function removeCity() {

    let index = $(this).closest('tr').index();
    console.log(index);
}

window.removeCity = removeCity;