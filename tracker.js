// JavaScript tracking code

async function track(event='page_view', buttonsIds=[]) {
    let url = encodeURIComponent(window.location.href);
    let sessionId = sessionStorage.getItem("sessionId") ?? '';
    let api = 'https://' + window.location.host + '/api.php';

    if ((event==="button_click") && (buttonsIds.length > 0)) {
        document.getElementById("myButton").addEventListener("click", async function (url, sessionId) {
            // Send data to the server using fetch API
            await fetch(api, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded' // Important for PHP to parse
                },
                body: 'event=button_click&url=' + url + '&session=' + sessionId,
            })
                .then(response => response.text()) // Optional: Handle server response
                .then(data => {
                    console.log('Tracking data sent:', data); // Log the response
                })
                .catch(error => {
                    console.error('Error sending tracking data:', error);
                });
        });
    }

    if (event==='page_view') {
        await fetch(api, {
            method: 'POST',
            headers: {
                //'Content-Type': 'application/x-www-form-urlencoded',
                "Content-Type": "application/json",
            },
            body: 'event=page_view&page=' + url + '&session=' + sessionId,
        })
            .then(response => response.text())
            .then(data => {
                console.log('Page view tracked:', data);
            })
            .catch(error => {
                console.error('Error tracking page view:', error);
            });
    }

    return null;
}

track();
