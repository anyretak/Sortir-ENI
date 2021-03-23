var mymap = L.map('mapid').setView([48.8566, 2.3522], 13);
var mylayer = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox/streets-v11',
    tileSize: 512,
    zoomOffset: -1,
    accessToken: 'pk.eyJ1IjoiYW55cmV0YWsiLCJhIjoiY2ttbWF6cXVsMDVzbzJvcGU1dnV0aTI1cCJ9.PIxd_vT-cCEcZs85lHDrEA'
});
mylayer.addTo(mymap);

document.querySelector('#app-campus').addEventListener('change', () => {
    const campus = document.querySelector('#app-campus').value;

    if (campus === 'Bordeaux') {
        mymap.remove();
        mymap = L.map('mapid').setView([44.8389, -0.5775], 13);
    } else if (campus === 'Paris') {
        mymap.remove();
        mymap = L.map('mapid').setView([48.8566, 2.3522], 13);
    } else if (campus === 'Biarritz') {
        mymap.remove();
        mymap = L.map('mapid').setView([43.4824, -1.5594], 13);
    }
    mylayer.addTo(mymap);

    fetch('http://localhost/sortir-eni/public/api/map', {
        method: "POST",
        body: JSON.stringify({
            'campus': campus,
        }),
    })
        .then(response => response.json())
        .then((data) => {
            data = JSON.parse(data);
            data.forEach(function (event) {
                var marker = L.marker([event.lat, event.long]).addTo(mymap);
                marker.bindPopup(event.name).openPopup();
            });
        });
})


