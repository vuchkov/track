// JavaScript tracking code

async function track(event='page_view', buttonsIds=[]) {
    if ((event==="button_click") && (buttonsIds.length > 0)) {
        let url = encodeURIComponent(window.location.href);
        let sessionId = sessionStorage.getItem("sessionId");
        document.getElementById("myButton").addEventListener("click", async function (url, sessionId) {
            // Send data to the server using fetch API
            await fetch('track.php', {
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
        let url = encodeURIComponent(window.location.href);
        let sessionId = sessionStorage.getItem("sessionId");

        await fetch('track.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
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
}
