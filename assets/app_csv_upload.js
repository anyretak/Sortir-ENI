document.querySelector('#app-csv-submit').addEventListener('click', () => {
    const link = document.querySelector('#app-csv-link').value;
    console.log(link);

    fetch('http://localhost/sortir-eni/public/admin/csv_upload', {
        method: 'POST',
        body: JSON.stringify({
            'link': link,
        })
    })
        .then(() => {
            let message = document.querySelector('#app-flash-message');
            message.innerHTML = 'User group has been registered!';
        })
})