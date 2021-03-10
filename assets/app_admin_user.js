document.addEventListener('click', (e) => {
    if (e.target && e.target.id === 'app-user-suspend') {
        let target = e.target;
        let userData = target.dataset.userData;
        let tableMain = document.getElementById('app-user-table');
        let tableFill = tableMain.firstElementChild.nextSibling;

        fetch('http://localhost/sortir-eni/public/admin/user_suspend', {
            method: 'POST',
            body: JSON.stringify({
                'user': userData,
            })
        })
            .then(response => response.text())
            .then(data => {
                tableFill.innerHTML = data;
            })
    }
})

document.addEventListener('click', (e) => {
    if (e.target && e.target.id === 'app-user-delete') {
        let target = e.target;
        let userData = target.dataset.userData;
        let parent = target.parentElement.parentElement;

        fetch('http://localhost/sortir-eni/public/admin/user_delete', {
            method: 'POST',
            body: JSON.stringify({
                'user': userData,
            })
        })
            .then(() => {
                parent.remove();
            })
    }
})