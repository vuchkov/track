async function tracker(event='page_view', buttonsIds=[]) {
    //const api = "https://your-server-domain.com/api.php";
    const api = 'https://' + window.location.host + '/api.php';

    //const url = encodeURIComponent(window.location.href);
    const url = window.location.href;
    /*const referrer = encodeURIComponent(document.referrer);
    const userAgent = encodeURIComponent(navigator.userAgent);*/

    if (event==='page_view') {
        await fetch(api, {
            method: 'POST',
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ event, url })
        })
            .then(response => response.text())
            .then(data => {
                console.log('Page view tracked:', data);
            })
            .catch(error => {
                console.error('Error tracking page view:', error);
            });
    }

    // You can add more tracking options like:
    if ((event==="button_click") && (buttonsIds.length > 0)) {
        buttonsIds.forEach((buttonId) => {
            document.getElementById(buttonId).addEventListener("click",
                async function (event, url) {
                    await fetch(api, {
                        method: 'POST',
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({ event, url })
                    })
                        /*.then(response => response.text())
                        .then(data => {
                            console.log('Tracking data sent:', data);
                        })
                        .catch(error => {
                            console.error('Error sending tracking data:', error);
                        });*/
            });
        });
    }

    return null;
}

// Run tracker.
tracker();
