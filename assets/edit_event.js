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

    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/ajax/location_data',
        data: {
            'city': city,
        },
        success: function (data) {
            let response = JSON.parse(data);
            console.log(response);
            let location = document.getElementById("selectLocation");
            let options = "";
            options = `<option value=""></option>`
            response.map(locations => {
                options += `<option value="${locations.name}">${locations.name}</option>`
            })
            location.innerHTML = options;
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


