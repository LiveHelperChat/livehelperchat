var LHCOperatorNotifications = (function() {

    function LHCOperatorNotifications(params) {
        const publicKey = params.public_key;

        if ('serviceWorker' in navigator && 'PushManager' in window) {
            // Register service worker
            let swRegistration;
            navigator.serviceWorker.register(WWW_DIR_JAVASCRIPT + 'notifications/serviceworkerop')
                .then(registration => {
                    if (registration.installing) {
                        console.log('Service worker installing');
                    } else if (registration.waiting) {
                        console.log('Service worker installed');
                    } else if (registration.active) {
                        console.log('Service worker active');
                    }

                    console.log('Service Worker registered');

                    swRegistration = registration;
                    return registration.pushManager.getSubscription();
                })
                .then(subscription => {
                    document.getElementById('subscribe-persistent').addEventListener('click', () => {
                        subscribeUser(swRegistration);
                    });
                });
        }

        async function subscribeUser(registration) {
            const subscriptionOptions = {
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(publicKey)
            };
            try {
                const subscription = await registration.pushManager.subscribe(subscriptionOptions);

                await fetch(WWW_DIR_JAVASCRIPT + 'notifications/subscribeop', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(subscription),
                    credentials: 'same-origin'
                });
                loadSubscriptions();
            } catch (error) {
                console.log(error);
                alert('Subscription failed: ' + error.message);
            }
        }

        function loadSubscriptions(){
            fetch(WWW_DIR_JAVASCRIPT + 'notifications/loadsubscriptions', {
                method: 'GET',
                credentials: 'same-origin'
            }).then((response) => response.text())
                .then((html) => {
                    document.getElementById('subscriptions').innerHTML = html;
                    lhinst.protectCSFR();
                }).catch((error) => {
                console.error('Error loading subscriptions:', error);
            });
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        loadSubscriptions();
    }

    return LHCOperatorNotifications;
})();

