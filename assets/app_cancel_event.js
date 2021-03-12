document.querySelector('#app-cancel-event').addEventListener('click', () => {
    const reason = document.querySelector('#cancel-reason').value;
    const event = document.querySelector('#cancel-reason-event').innerText;

    fetch('http://localhost/sortir-eni/public/api/cancel_event', {
        method: 'POST',
        body: JSON.stringify({
            'reason': reason,
            'event': event,
        }),
    })
        .then(() => {
            let message = document.querySelector('#app-flash-message');
            message.innerHTML = 'Event has been cancelled!';
        });
})