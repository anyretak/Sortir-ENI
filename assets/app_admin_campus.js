document.querySelector('#app-campus-add').addEventListener('click', () => {
    const campusName = document.querySelector('#app-campus-name').value;
    const table = document.querySelector('#app-campus-table');
    const rows = table.rows.length;

    fetch('http://localhost/sortir-eni/public/admin/add_campus', {
        method: 'POST',
        body: JSON.stringify({
            'campus': campusName,
        }),
    })
        .then(() => {
            let index = rows - 1;
            let row = table.insertRow(index);
            let cell1 = row.insertCell(0);
            let cell2 = row.insertCell(1);
            cell1.innerHTML = campusName;
            cell2.innerHTML = "<a href=\"\">Edit</a><span class=\"app-nav\" id=\"app-campus-remove\" data-campus-name = \""+campusName+"\">Remove</span>";
        });
})

document.addEventListener('click', (e) => {
    if (e.target && e.target.id === 'app-campus-remove') {
        const target = e.target;
        const parent = target.parentElement.parentElement;
        const campus = target.dataset.campusName;

        fetch('http://localhost/sortir-eni/public/admin/delete_campus', {
            method: 'POST',
            body: JSON.stringify({
                'campus': campus,
            })
        })
            .then(() => {
                parent.remove();
            });
    }
})
