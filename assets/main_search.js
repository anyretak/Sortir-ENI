function filterCampus() {
    let tableMain = document.getElementById('app-main-table');
    let tableFill = tableMain.firstElementChild.nextSibling;
    let input = document.getElementById("searchCampus").value;

    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/ajax/campus_filter',
        data: {
            'campus': input,
        },

        success: function (response) {
            console.log('Hello Again');
            tableFill.innerHTML = response;
        }
    });
}

window.filterCampus = filterCampus;

function nameSearchFunction() {
    let table = document.getElementById("app-main-table");
    let tr = table.getElementsByTagName("tr");
    let input = document.getElementById("app-input");
    let filter = input.value.toUpperCase();
    for (let i = 0; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            let txtValue = td.textContent || td.innerText;
            let status = tr[i].style.display;
            if (txtValue.toUpperCase().indexOf(filter) > -1 && status !== 'none') {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

window.nameSearchFunction = nameSearchFunction;

function dateFilter() {
    let tableMain = document.getElementById('app-main-table');
    let tableFill = tableMain.firstElementChild.nextSibling;
    let dateMinValue = new Date(document.getElementById("searchFrom").value);
    let dateMaxValue = new Date(document.getElementById("searchTo").value);
    dateMinValue.setHours(0);
    dateMaxValue.setHours(0);

    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/ajax/date_filter',
        data: {
            'from': dateMinValue,
            'to': dateMaxValue,
        },

        success: function (response) {
            console.log('Hello Again');
            tableFill.innerHTML = response;
        }
    });

}

window.dateFilter = dateFilter;

function filterUser(e) {
    let table = document.getElementById("app-main-table");
    let tr = table.getElementsByTagName("tr");
    let input = document.getElementById("_type_1");
    let target = e.target;
    let user = target.dataset.thisUser;

    for (let i = 0; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName("td")[6];
        if (td) {
            let cellValue = td.textContent;
            if (!input.checked) {
                tr[i].style.display = "";
            } else if (cellValue === user && input.checked) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

window.filterUser = filterUser;

function filterSubscribed() {
    let table = document.getElementById("app-main-table");
    let tr = table.getElementsByTagName("tr");
    let input = document.getElementById("_type_2");
    for (let i = 0; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName("td")[5];
        if (td) {
            let cellValue = td.textContent.trim();
            if (!input.checked) {
                tr[i].style.display = "";
            } else if (cellValue === 'X' && input.checked) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

window.filterSubscribed = filterSubscribed;

function filterNotSubscribed() {
    let table = document.getElementById("app-main-table");
    let tr = table.getElementsByTagName("tr");
    let input = document.getElementById("_type_3");
    for (let i = 0; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName("td")[5];
        if (td) {
            let cellValue = td.textContent.trim();
            if (!input.checked) {
                tr[i].style.display = "";
            } else if (cellValue !== 'X' && input.checked) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

window.filterNotSubscribed = filterNotSubscribed;

function filterPassed(e) {
    let table = document.getElementById("app-main-table");
    let tr = table.getElementsByTagName("tr");
    let input = document.getElementById("_type_4");

    for (let i = 0; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName("td")[4];
        if (td) {
            let cellValue = td.textContent;
            if (!input.checked) {
                tr[i].style.display = "";
            } else if (cellValue === "Finished" && input.checked) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }

}

window.filterPassed = filterPassed;

function userSubscribe(e) {
    let tableMain = document.getElementById('app-main-table');
    let tableFill = tableMain.firstElementChild.nextSibling;
    let target = e.target;
    let subUser = target.dataset.userSub;
    let subEvent = target.dataset.eventSub;

    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/ajax/user_sub',
        data: {
            'user': subUser,
            'event': subEvent,
        },

        success: function (response) {
            console.log('Hello');
            tableFill.innerHTML = response;
        }
    });

}

window.userSubscribe = userSubscribe;

function userUnsubscribe(e) {
    let tableMain = document.getElementById('app-main-table');
    let tableFill = tableMain.firstElementChild.nextSibling;
    let target = e.target;
    let subUser = target.dataset.userSub;
    let subEvent = target.dataset.eventSub;

    $.ajax({
        type: "POST",
        url: 'http://localhost/sortir-eni/public/ajax/user_unsub',
        data: {
            'user': subUser,
            'event': subEvent,
        },

        success: function (response) {
            console.log('Hello Again');
            tableFill.innerHTML = response;
        }
    });
}

window.userUnsubscribe = userUnsubscribe;