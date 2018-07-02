var notificationsLHC = {
    sendNotification : function() {
        if (!!window.postMessage) {
            parent.postMessage('lhc_notification:just_testing', '*');
        }
    }
}