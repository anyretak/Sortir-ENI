document.querySelector('#selectCity').addEventListener('change', () => {
    const cityId = document.querySelector('#selectCity').value;
    const code = document.querySelector('#updateCode');

    fetch('http://localhost/sortir-eni/public/api/city', {
        method: "POST",
        body: JSON.stringify({
            'cityId': cityId,
        }),
    })
        .then(response => response.json())
        .then((data) => {
            code.value = data.code;
        });

    fetch('http://localhost/sortir-eni/public/api/location_filter', {
        method: "POST",
        body: JSON.stringify({
            'cityId': cityId,
        }),
    })
        .then(response => response.json())
        .then((data) => {
            let location = document.querySelector('#selectLocation');
            let options = "";
            options = `<option value=""></option>`
            data.map(locations => {
                options += `<option value="${locations.name}">${locations.name}</option>`
            })
            location.innerHTML = options;
        });
})

document.querySelector('#selectLocation').addEventListener('change', () => {
    const locId = document.querySelector('#selectLocation').value;
    const street = document.querySelector('#updateStreet');
    const lat = document.querySelector('#updateLat');
    const long = document.querySelector('#updateLong');

    fetch('http://localhost/sortir-eni/public/api/location', {
        method: "POST",
        body: JSON.stringify({
            'locId': locId,
        }),
    })
        .then(response => response.json())
        .then((data) => {
            street.value = data.street;
            lat.value = data.latitude;
            long.value = data.longitude;
        });
})


