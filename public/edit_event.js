function addCity() {
    let cityName = document.getElementById("app-js-city-name").value;
    let cityCode = document.getElementById("app-js-city-code").value;

    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/admin/edit_city',
        data: {
            city: cityName,
            code: cityCode
        },
        success: function () {
            let rowCount = $('#app-city-table tr').length;
            let index = rowCount - 1;
            let table = document.getElementById("app-city-table");
            let row = table.insertRow(index);
            let cell1 = row.insertCell(0);
            let cell2 = row.insertCell(1);
            let cell3 = row.insertCell(2);
            cell1.innerHTML = cityName;
            cell2.innerHTML = cityCode;
            cell3.innerHTML = "<a href=\"\">Edit</a><span class=\"app-nav\" onclick=\"removeCity(event)\">Remove</span>";
        }
    });
}

window.addCity = addCity;

function removeCity(e) {

    let target = e.target;
    let node = target.parentElement.previousElementSibling.innerHTML;
    let parent = target.parentElement.parentElement;

    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/admin/mod_city',
        data: {
            'code': node,
        },
        success: function (data) {
            console.log(data);
            parent.remove();
        }
    });
}

window.removeCity = removeCity;

function updateCity() {

    let city = document.getElementById("selectCity").value;
    let code = document.getElementById("updateCode");

    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/ajax/city_event',
        data: {
            'city': city,
        },
        success: function (data) {
            let response = JSON.parse(data);
            code.value = response.code;
        }
    });

}

window.updateCity = updateCity;

function updateLocation() {

    let locId = document.getElementById("selectLocation").value;
    let street = document.getElementById("updateStreet");
    let lat = document.getElementById("updateLat");
    let long = document.getElementById("updateLong");

    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/ajax/location_event',
        data: {
            'locId': locId,
        },
        success: function (data) {
            let response = JSON.parse(data);
            street.value = response.street;
            lat.value = response.latitude;
            long.value = response.longitude;
        }
    });
}

window.updateLocation = updateLocation;

function addCampus() {
    let campusName = document.getElementById("app-js-campus-name").value;

    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/admin/edit_campus',
        data: {
            campus: campusName,
        },
        success: function () {
            let rowCount = $('#app-campus-table tr').length;
            let index = rowCount - 1;
            let table = document.getElementById("app-campus-table");
            let row = table.insertRow(index);
            let cell1 = row.insertCell(0);
            let cell2 = row.insertCell(1);
            cell1.innerHTML = campusName;
            cell2.innerHTML = "<a href=\"\">Edit</a><span class=\"app-nav\" onclick=\"removeCampus(event)\">Remove</span>";
        }
    });
}

window.addCampus = addCampus;

function removeCampus(e) {
    let target = e.target;
    let node = target.parentElement.previousElementSibling.innerHTML;
    let parent = target.parentElement.parentElement;

    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/admin/mod_campus',
        data: {
            'campus': node,
        },
        success: function (data) {
            console.log(data);
            parent.remove();
        }
    });
}

window.removeCampus = removeCampus;

function cancelEvent() {
    let reason = document.getElementById("cancel-reason").value;
    let event = document.getElementById("cancel-reason-event").innerText;
    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/ajax/cancel_event',
        data: {
            'reason': reason,
            'event': event,
        },
        success: function (data) {
            console.log(data);
        }
    });
}

window.cancelEvent = cancelEvent;

function suspendUser(e) {
    let tableMain = document.getElementById('app-user-table');
    let tableFill = tableMain.firstElementChild.nextSibling
    let target = e.target;
    let userData = target.dataset.userData;

    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/admin/user_suspend',
        data: {
            'user': userData,
        },
        success: function (data) {
            console.log('Hello Again');
            tableFill.innerHTML = data;
        }
    });
}

function deleteUser(e) {
    let target = e.target;
    let userData = target.dataset.userData;
    let parent = target.parentElement.parentElement;

    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/admin/user_delete',
        data: {
            'user': userData,
        },
        success: function (data) {
            console.log('Hello Again');
            parent.remove();
        }
    });
}

window.deleteUser = deleteUser;


