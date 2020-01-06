
class _chatNotifications {
    constructor() {
        this.isNotificationsSubscribed = false;
        this.applicationServerPublicKey = null;
        this.eventEmitter = null;
    }

    setPublicKey(publicKey, eventEmitter) {
        this.applicationServerPublicKey = publicKey
        this.eventEmitter = eventEmitter
    }

   sendNotification() {

    var that = this;
    var applicationServerPublicKey = this.applicationServerPublicKey;

    var swRegistration = null;

    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        alert("This browser does not support desktop notification");
        return;
    }

    // Let's check if the browser supports notifications
    if (!("Notification" in window)) {
        alert("This browser does not support desktop notification");
        return;
    }
    // Let's check whether notification permissions have already been granted
    else if (Notification.permission === "granted") {
        // If it's okay let's create a notification
        //
    }
    // Otherwise, we need to ask the user for permission
    else if (Notification.permission !== "denied") {
        Notification.requestPermission(function (permission) {
            // If the user accepts, let's create a notification
            if (permission !== "granted") {
                alert('Sorry but you have denied notification!');
                return;
            }
        });
    } else if (Notification.permission === "denied") {
        alert('Sorry but you have denied notification!');
        return;
    }

    function urlB64ToUint8Array(base64String) {
        var padding = '='.repeat((4 - base64String.length % 4) % 4);
        var base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

        var rawData = window.atob(base64);
        var outputArray = new Uint8Array(rawData.length);

        for (var i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    function updateSubscriptionOnServer(subscription, subscribe) {
        if (subscription === null) {
            return;
        }

        var key = subscription.getKey('p256dh');
        var token = subscription.getKey('auth');
        var contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

        var payload = JSON.stringify({
            endpoint: subscription.endpoint,
            publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
            authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
            contentEncoding : contentEncoding
        });

        that.eventEmitter.emitEvent('subcribedEvent', [{'payload' : payload}]);
    }

    var that = this;

    function subscribeUser() {
        var applicationServerKey = urlB64ToUint8Array(applicationServerPublicKey);
        swRegistration.pushManager.subscribe({
            'userVisibleOnly': true,
            'applicationServerKey': applicationServerKey
        }).then(function(subscription) {
            updateSubscriptionOnServer(subscription, true);
            that.isNotificationsSubscribed = true;
        });
    }

    function unsubscribeUser() {
        swRegistration.pushManager.getSubscription().then(function(subscription) {
            if (subscription) {
                updateSubscriptionOnServer(subscription,false);
                return subscription.unsubscribe();
            }
        }).then(function() {
            alert('You have unsubscribed!');
            that.isNotificationsSubscribed = false;
        });
    }

    function initializeUI() {
        // Set the initial subscription value
        swRegistration.pushManager.getSubscription().then(function(subscription) {
            that.isNotificationsSubscribed = !(subscription === null);
            if (that.isNotificationsSubscribed) {
                subscribeUser();
            } else {
                subscribeUser();
            }
        });
    }

    // At last, if the user has denied notifications, and you
    // want to be respectful there is no need to bother them any more.
    navigator.serviceWorker.register('/sw.lhc.js?v=2').then(function(swReg) {
        swRegistration = swReg;
        initializeUI();
    });

    }

};

const chatNotifications = new _chatNotifications();
export { chatNotifications };