var notificationsLHC = {
    sendNotification : function() {
        if (!!window.postMessage && typeof(parent) !== 'undefined') {
            parent.postMessage('lhc_notification:just_testing', '*');
        }
    }
}