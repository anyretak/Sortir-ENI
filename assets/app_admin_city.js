document.querySelector('#app-city-add').addEventListener('click', () => {
    const cityName = document.querySelector('#app-city-name').value;
    const cityCode = document.querySelector('#app-city-code').value;
    const table = document.querySelector('#app-city-table');
    const rows = table.rows.length;

    fetch('http://localhost/sortir-eni/public/admin/add_city', {
        method: 'POST',
        body: JSON.stringify({
            'city': cityName,
            'code': cityCode,
        }),
    })
        .then(() => {
            let index = rows - 1;
            let row = table.insertRow(index);
            let cell1 = row.insertCell(0);
            let cell2 = row.insertCell(1);
            let cell3 = row.insertCell(2);
            cell1.innerHTML = cityName;
            cell2.innerHTML = cityCode;
            cell3.innerHTML = "<a href=\"\">Edit</a><span class=\"app-nav\" id=\"app-city-remove\" data-city-name = \""+cityName+"\">Remove</span>";
        });
})

document.addEventListener('click', (e) => {
    if (e.target && e.target.id === 'app-city-remove') {
        const target = e.target;
        const parent = target.parentElement.parentElement;
        const city = target.dataset.cityName;

        fetch('http://localhost/sortir-eni/public/admin/delete_city', {
            method: 'POST',
            body: JSON.stringify({
                'city': city,
            })
        })
            .then(() => {
                parent.remove();
            });
    }
})