document.querySelector('#app-search-bt').addEventListener('click', () => {
    const campus = document.querySelector('#app-campus').value;
    const event = document.querySelector('#app-input').value;
    const dateFrom = document.querySelector('#app-date-from').value;
    const dateTo = document.querySelector('#app-date-to').value;
    const user = document.querySelector('.app-user-filter').checked;
    const userSub = document.querySelector('.app-user-sub').checked;
    const userNonsub = document.querySelector('.app-user-nonsub').checked;
    const past = document.querySelector('.app-past').checked;
    let tableMain = document.querySelector('#app-main-table');
    let tableFill = tableMain.firstElementChild.nextSibling;

    fetch('http://localhost/sortir-eni/public/api/main_filter', {
        method: 'POST',
        body: JSON.stringify({
            'campusName': campus,
            'event': event,
            'dateFrom': dateFrom,
            'dateTo': dateTo,
            'userName': user,
            'userSub': userSub,
            'userNonsub': userNonsub,
            'past': past,
        })
    })
        .then(response => response.text())
        .then(data => {
            tableFill.innerHTML = data;
        })
})

document.addEventListener('click', (e) => {
    if (e.target && e.target.id === 'userSub') {
        let target = e.target;
        let subUser = target.dataset.userSub;
        let subEvent = target.dataset.eventSub;
        let tableMain = document.querySelector('#app-main-table');
        let tableFill = tableMain.firstElementChild.nextSibling;

        fetch('http://localhost/sortir-eni/public/api/user_sub', {
            method: 'POST',
            body: JSON.stringify({
                'user': subUser,
                'event': subEvent,
            })
        })
            .then(response => response.text())
            .then(data => {
                tableFill.innerHTML = data;
            })
    }
})